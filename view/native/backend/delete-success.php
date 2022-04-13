<?php //>

$controller->response()->json(['type' => 'refresh', 'modal' => true, 'message' => i18n('backend.delete-success'), 'sublist' => @$form['sublist']]);
