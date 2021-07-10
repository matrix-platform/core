<?php //>

namespace matrix\web\backend\bundle;

use matrix\web\backend\Controller;

class GetCustomBundle extends Controller {

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
            'view' => 'backend/bundle/form.php',
        ];
    }

    protected function process($form) {
        $folder = $this->folder();
        $file = $this->file();
        $default = union_resource("{$folder}/{$file}.php");

        return [
            'success' => true,
            'data' => array_replace_recursive($default, load_data("{$folder}/{$file}")),
            'default' => $default,
            'path' => $this->node(),
            'prefix' => $this->prefix(),
        ];
    }

}
