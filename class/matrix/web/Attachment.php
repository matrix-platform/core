<?php //>

namespace matrix\web;

use Mhor\MediaInfo\MediaInfo;

class Attachment {

    private static $files = [];

    public static function cleanup() {
        foreach (self::$files as $file) {
            unlink($file);
        }

        self::$files = [];
    }

    public static function from($filename, $content, $description, $privilege = null) {
        $folder = $privilege ? (PRIV_FILES_HOME . 'p') : FILES_HOME;

        if (preg_match('/^data:/', $content)) {
            $file = tempnam(create_folder($folder . date('Ymd')), '');
            $handle = fopen($file, 'w');
            $raw = tmpfile();

            fwrite($raw, $content);
            fseek($raw, strpos($content, ',') + 1);
            stream_filter_append($handle, 'convert.base64-decode', STREAM_FILTER_WRITE);
            stream_copy_to_stream($raw, $handle);
            fclose($handle);
            fclose($raw);
        } else if (file_exists($content)) {
            $file = tempnam(create_folder($folder . date('Ymd')), '');
            rename($content, $file);
        } else {
            return null;
        }

        chmod($file, 0644);

        $instance = new Attachment($filename, $file, $description, $privilege);

        self::$files[] = $instance->info['file'];

        return $instance;
    }

    public static function validate($files, $type) {
        if ($type !== null) {
            foreach ($files as $file) {
                if ($file instanceof Attachment) {
                    if (!preg_match("/^{$type}$/", $file->info['mime_type'])) {
                        return false;
                    }
                } else {
                    $file = model('File')->find(['path' => $file]);

                    if (!$file || !preg_match("/^{$type}$/", $file['mime_type'])) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private $info;

    public function __construct($name, $file, $description = null, $privilege = null) {
        if (strtolower(pathinfo($name, PATHINFO_EXTENSION)) === 'svg') {
            $svg = "{$file}.svg";
            rename($file, $svg);
            $file = $svg;
            $mime_type = 'image/svg+xml';
        } else {
            $mime_type = mime_content_type($file);
        }

        $info = [
            'file' => $file,
            'type' => 2,
            'name' => $name ?: 'NO_NAME',
            'path' => substr($file, strlen(dirname($file, 2)) + 1),
            'size' => filesize($file),
            'hash' => md5_file($file),
            'description' => $description,
            'mime_type' => $mime_type,
            'privilege' => $privilege,
        ];

        switch (strstr($mime_type, '/', true)) {
        case 'audio':
        case 'video':
            $media = (new MediaInfo())->getInfo($file, true);
            $videos = $media->getVideos();
            if ($videos) {
                $info['height'] = $videos[0]->get('height')->getAbsoluteValue();
                $info['width'] = $videos[0]->get('width')->getAbsoluteValue();
            }
            $data = $media->getGeneral();
            $info['mime_type'] = $data->get('internet_media_type');
            $info['seconds'] = ceil($data->get('duration')->getMilliseconds() / 1000);
            break;
        case 'image':
            $size = @getimagesize($file);
            if ($size) {
                $info['width'] = $size[0];
                $info['height'] = $size[1];
            }
            break;
        }

        $this->info = $info;
    }

    public function __toString() {
        return $this->info['path'];
    }

    public function getInfo() {
        return $this->info;
    }

    public function save($parent_id = -1) {
        $duplicated = model('File')->query([
            'parent_id' => $parent_id,
            'size' => $this->info['size'],
            'hash' => $this->info['hash'],
        ]);

        if ($duplicated) {
            unlink($this->info['file']);

            $this->info = $duplicated[0];
        } else {
            $this->info['parent_id'] = $parent_id;

            $this->info = model('File')->insert($this->info);
        }

        return $this->info['path'];
    }

}
