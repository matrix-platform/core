<?php //>

use matrix\web\Attachment;

return function ($value, $options) {
    $values = is_array($value) ? $value : [$value];

    return Attachment::validate($values, @$options['mimeType'] ?: 'image\/[\w+]+');
};
