<?php //>

$message = i18n('backend.save-success');

if (@$form['form-type'] === 'modal') {
    $result = ['type' => 'refresh', 'modal' => true, 'message' => $message, 'sublist' => @$form['sublist']];
} else {
    $result = ['type' => 'backward', 'backward' => @$form['r'], 'message' => $message];
}

$controller->response()->json($result);
