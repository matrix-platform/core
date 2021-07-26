<?php //>

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function setValue($sheet, $x, $y, $value) {
    if (is_string($value)) {
        $sheet->setCellValueExplicitByColumnAndRow($x, $y, $value, DataType::TYPE_STRING);
    } else {
        $sheet->setCellValueByColumnAndRow($x, $y, $value);
    }
}

$table = $controller->table();
$columns = $controller->getColumns();

$sheets = new Spreadsheet();
$sheet = $sheets->getActiveSheet();

$x = 1;
$y = 1;

foreach ($columns as $name => $column) {
    setValue($sheet, $x++, $y, $column->label() ?: i18n("table/{$table->name()}.{$name}"));
}

foreach ($result['data'] as $data) {
    $x = 1;
    $y++;

    foreach ($columns as $name => $column) {
        $value = $data[$name];

        if (is_bool($value)) {
            $value = var_export($value, true);
        }

        $options = $column->options();

        if ($options && key_exists($value, $options)) {
            $value = $options[$value]['title'];
        }

        setValue($sheet, $x++, $y, $value);
    }
}

$file = tempnam(sys_get_temp_dir(), '');
$timestamp = date('YmdHis');
$title = $table->name();

$sheet->setTitle($title);

(new Xlsx($sheets))->save($file);

$result = [
    'type' => 'download',
    'filename' => "{$title}-{$timestamp}.xlsx",
    'content' => base64_encode(file_get_contents($file)),
    'contentType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
];

unlink($file);

resolve('raw.php')->render($controller, $form, $result);
