<?php //>

namespace matrix\view\twig;

use Twig\Markup;

class I18nText extends Markup {

    public function __construct($name, $data, $markup) {
        $content = i18n("lang.{$name}", $name);

        if ($data) {
            $content = render($content, $data);
        }

        if ($markup) {
            $key = htmlspecialchars($name);
            $content = "<i18n-text data-key=\"{$key}\">{$content}</i18n-text>";
        }

        parent::__construct($content, 'UTF-8');
    }

}
