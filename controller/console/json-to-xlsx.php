<?php //>

// php www/index.php /console/json-to-xlsx file=<FILENAME>

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

return new class() extends matrix\cli\Controller {

    private $mapping = [];

    protected function process($form) {
        $file = $form['file'];
        $data = json_decode(file_get_contents($file), true);

        $this->parse([], $data);

        //--

        $sheets = new Spreadsheet();
        $sheet = $sheets->getActiveSheet();
        $y = 1;

        foreach ($this->mapping as $name => $value) {
            $sheet->getCellByColumnAndRow(1, $y)->setValueExplicit($name, DataType::TYPE_STRING);
            $sheet->getCellByColumnAndRow(2, $y)->setValueExplicit($value, DataType::TYPE_STRING);
            $y++;
        }

        $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(2)->setAutoSize(true);

        (new Xlsx($sheets))->save('output.xlsx');

        //--

        return ['success' => true];
    }

    private function parse($prefix, $data) {
        foreach ($data as $name => $value) {
            $this->parseElement($prefix, $name, $value);
        }
    }

    private function parseElement($prefix, $name, $value) {
        $prefix[] = $name;

        if (is_array($value)) {
            $this->parse($prefix, $value);
        } else {
            $this->mapping[implode('.', $prefix)] = $value;
        }
    }

};
