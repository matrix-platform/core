<?php //>

// php index.php /console/find-rotated-image

return new class() extends matrix\cli\Controller {

    protected function process($form) {
        foreach (model('File')->query() as $file) {
            if (strtok($file['mime_type'], '/') === 'image') {
                $folder = $file['privilege'] ? PRIV_FILES_HOME : FILES_HOME;
                $source = "{$folder}{$file['path']}";
                $exif = @exif_read_data($source);

                switch (@$exif['Orientation']) {
                case 3:
                case 6:
                case 8:
                    echo "{$source}\n";
                    break;
                }
            }
        }

        return ['success' => true];
    }

};
