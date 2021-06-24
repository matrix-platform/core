<?php //>

$result = ['type' => 'refresh', 'modal' => true, 'message' => i18n('backend.delete-success')];

resolve('raw.php')->render($controller, $form, $result);
