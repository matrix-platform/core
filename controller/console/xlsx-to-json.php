<?php //>

// php www/index.php /console/xlsx-to-json file=<FILENAME>

use PhpOffice\PhpSpreadsheet\IOFactory;

return new class() extends matrix\cli\Controller {

    protected function process($form) {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($form['file']);
        $worksheet = $spreadsheet->getActiveSheet();

        $data = [];

        foreach ($worksheet->getRowIterator() as $row) {
            $values = [];

            foreach ($row->getCellIterator() as $cell) {
                $values[] = $cell->getValue();
            }

            $name = trim($values[0]);
            $text = trim($values[1]);

            if (!$name) {
                continue;
            }

            $mapping = &$data;
            $tokens = explode('.', $name);
            $name = array_pop($tokens);

            while ($tokens) {
                $token = array_shift($tokens);

                if (!key_exists($token, $mapping)) {
                    $mapping[$token] = [];
                }

                $mapping = &$mapping[$token];
            }

            $mapping[$name] = $text;
        }

        file_put_contents('output.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return ['success' => true];
    }

};
