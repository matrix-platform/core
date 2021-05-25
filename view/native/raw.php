<?php //>

if (PHP_SAPI !== 'cli') {
    header('Content-Type: application/json; charset=UTF-8');
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
