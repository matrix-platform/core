<?php //>

$data = $result['data'];

$result = [
    'type' => 'file-info',
    'id' => $data['id'],
    'name' => $data['name'],
    'description' => $data['description'],
    'message' => i18n('backend.save-success'),
];

$controller->response()->json($result);
