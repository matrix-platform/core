<?php //>

namespace matrix\view;

use Twig\Environment;
use Twig\Extension\StringLoaderExtension;
use Twig\Extra\String\StringExtension;
use Twig\Loader\FilesystemLoader;

class Twig {

    private $view;

    public function __construct($view) {
        $this->view = $view;
    }

    public function render($controller, $form, $result) {
        $paths = [];

        foreach (RESOURCE_FOLDERS as $folder) {
            $path = $folder . 'view/twig/';

            if (is_dir($path)) {
                $paths[] = $path;
            }
        }

        if (PHP_SAPI === 'cli') {
            $options = [];
        } else {
            $options = ['auto_reload' => true, 'cache' => APP_DATA . 'twig'];
        }

        $twig = new Environment(new FilesystemLoader($paths), $options);

        $twig->addExtension(new StringExtension());
        $twig->addExtension(new StringLoaderExtension());

        $twig->registerUndefinedFilterCallback(function ($name) {
            return load_resource("include/twig/filter/{$name}.php") ?: false;
        });

        $twig->registerUndefinedFunctionCallback(function ($name) {
            return load_resource("include/twig/function/{$name}.php") ?: false;
        });

        echo $twig->render($this->view, [
            'controller' => $controller,
            'form' => $form,
            'result' => $result,
        ]);
    }

}
