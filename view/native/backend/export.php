<?php //>

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function setValue($sheet, $x, $y, $value, $column = null) {
    if (is_string($value)) {
        $url = null;
        $wrap = false;

        if ($column) {
            switch ($column->listStyle()) {
            case 'email':
                $url = "mailto:{$value}";
                break;
            case 'file':
            case 'image':
                if ($column->multiple()) {
                    $files = [];
                    foreach (preg_split('/,/', $value, 0, PREG_SPLIT_NO_EMPTY) as $token) {
                        $files[] = url(APP_PATH . 'files/' . $token);
                    }
                    $value = implode("\n", $files);
                    $wrap = true;
                } else {
                    $file = load_file_data($value);
                    if ($file) {
                        $url = url(APP_PATH . 'files/' . $value);
                        $value = $file['name'];
                    }
                }
                break;
            case 'url':
                $url = $value;
                break;
            }
        }

        $cell = $sheet->getCellByColumnAndRow($x, $y);
        $cell->setValueExplicit($value, DataType::TYPE_STRING);

        if ($url) {
            $cell->getHyperlink()->setUrl($url);
        }

        if ($wrap) {
            $cell->getStyle()->getAlignment()->setWrapText(true);
        }
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

$headerStyle = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
];

foreach ($columns as $name => $column) {
    setValue($sheet, $x, $y, $column->label() ?: i18n("table/{$table->name()}.{$name}"));

    $sheet->getCellByColumnAndRow($x, $y)->getStyle()->applyFromArray($headerStyle);

    $x++;
}

foreach ($result['data'] as $data) {
    $x = 1;
    $y++;

    foreach ($columns as $name => $column) {
        $value = $data[$name];

        if ($value !== null) {
            if (is_bool($value)) {
                $value = var_export($value, true);
            }

            $options = $column->options();

            if ($options) {
                $values = [];

                foreach (preg_split('/,/', $value, 0, PREG_SPLIT_NO_EMPTY) as $token) {
                    if (key_exists($token, $options)) {
                        $values[] = $options[$token]['title'];
                    }
                }

                if ($values) {
                    $value = implode(',', $values);
                }
            }

            setValue($sheet, $x, $y, $value, $column);
        }

        $x++;
    }
}

foreach (range(1, count($columns)) as $x) {
    $sheet->getColumnDimensionByColumn($x)->setAutoSize(true);
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
