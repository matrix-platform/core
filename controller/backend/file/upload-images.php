<?php //>

use matrix\utility\Fn;

return new class() extends matrix\web\UserController {

    protected function wrap() {
        $form = parent::wrap();
        $images = [];

        foreach ($form['images'] as $image) {
            $images[] = $this->wrapFile($image, 'file')['file'];
        }

        $form['images'] = $images;

        return $form;
    }

    protected function process($form) {
        $paths = [];

        foreach ($form['images'] as $image) {
            $image->save();

            $paths[] = 'files/' . Fn::optimize_image(strval($image));
        }

        return ['success' => true, 'type' => 'insert-images', 'target' => @$form['target'], 'paths' => $paths];
    }

};
