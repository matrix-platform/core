<?php //>

use matrix\web\Attachment;

return new class() extends matrix\web\UserController {

    public function verify() {
        return true;
    }

    protected function wrap() {
        $form = parent::wrap();
        $file = @$form['upload'][0];

        if ($file) {
            $form['file'] = Attachment::from(@$file['name'], @$file['path'], null);
        }

        return $form;
    }

    protected function process($form) {
        $file = @$form['file'];

        if (!$file) {
            return ['success' => true, 'uploaded' => 0];
        }

        $info = $file->getInfo();

        if (strstr($info['mime_type'], '/', true) === 'image') {
            $url = 'images/' . base64_urlencode($file->save());
        } else {
            $url = 'files/' . $file->save();
        }

        return [
            'success' => true,
            'uploaded' => 1,
            'fileName' => $info['name'],
            'url' => $url,
        ];
    }

};
