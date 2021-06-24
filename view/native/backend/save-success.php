<?php //>

$message = i18n('backend.save-success');

if (@$form['form-type'] === 'modal') {
    $result = ['type' => 'refresh', 'modal' => true, 'message' => $message];
} else {
    $result = ['type' => 'backward', 'backward' => @$form['r'], 'message' => $message];
}

resolve('raw.php')->render($controller, $form, $result);
