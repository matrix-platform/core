<?php //>

use matrix\web\Attachment;

return new class() extends matrix\web\UserController {

    protected function wrap() {
        $form = parent::wrap();
        $images = [];

        foreach ($form['images'] as $image) {
            $images[] = Attachment::from($image['name'], $image['path'], null);
        }

        $form['images'] = $images;

        return $form;
    }

    protected function process($form) {
        $paths = [];

        foreach ($form['images'] as $image) {
            $paths[] = APP_PATH . 'images/' . base64_urlencode($image->save());
        }

        return ['success' => true, 'type' => 'insert-images', 'target' => @$form['target'], 'paths' => $paths];
    }

};
