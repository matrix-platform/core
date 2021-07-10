<?php //>

namespace matrix\web\backend\bundle;

use matrix\web\backend\Controller;

class UpdateCustomBundle extends Controller {

    use Form;

    public function __construct($path) {
        $tokens = preg_split('/\//', $path, 0, PREG_SPLIT_NO_EMPTY);

        if (count($tokens) === 2) {
            $category = $tokens[0];
            $folder = 'base';
            $file = $tokens[1];
        } else {
            list($category, $folder, $file) = $tokens;
        }

        $prefix = "{$category}/{$folder}.{$file}";
        $category = $category === 'i18n' ? ($category . '/' . LANGUAGE) : $category;
        $folder = $folder === 'base' ? $category : "{$category}/{$folder}";

        $this->values = [
            'folder' => $folder,
            'file' => $file,
            'prefix' => $prefix,
            'view' => 'backend/save-success.php',
        ];
    }

    protected function process($form) {
        return $this->save($form, $this->file(), $this->data());
    }

}
