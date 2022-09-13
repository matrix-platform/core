<?php //>

$message = i18n('backend.save-success');

if (@$form['form-type'] === 'modal') {
    if (@$form['refresh-after-save']) {
        $result = ['type' => 'refresh', 'modal' => true, 'message' => $message, 'sublist' => @$form['sublist']];
    } else {
        $result = ['type' => 'message', 'modal' => true, 'message' => $message];
    }
} else {
    $result = ['type' => 'backward', 'backward' => @$form['r'], 'message' => $message];
}

$controller->response()->json($result);
