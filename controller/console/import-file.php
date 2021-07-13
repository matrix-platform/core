<?php //>

// php index.php /console/import-file path=xxxxxxxx/xxxxxx [name=filename]

use matrix\utility\Fn;
use matrix\web\Attachment;

return new class() extends matrix\cli\Controller {

    protected function process($form) {
        $path = @$form['path'];

        if ($path === null) {
            return ['message' => 'invalid arguments'];
        }

        $folder = defined('FILES_HOME') ? FILES_HOME : (APP_HOME . 'www/files/');
        $file = "{$folder}{$path}";

        if (!is_file($file)) {
            return ['message' => "file `{$path}` not found"];
        }

        $model = model('File');

        if ($model->count(['path' => $path])) {
            return ['message' => "file `{$path}` exists"];
        }

        define('USER_ID', 0);

        $attachment = new Attachment(@$form['name'], $file);

        $info = $attachment->getInfo();
        $info['parent_id'] = -1;

        $model->insert($info);

        if (strtok($info['mime_type'], '/') === 'image' && strtok('/') !== 'svg+xml') {
            Fn::optimize_image($path);
        }

        return ['success' => true];
    }

};
