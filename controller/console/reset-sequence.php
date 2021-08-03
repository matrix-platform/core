<?php //>

// php index.php /console/reset-sequence

use matrix\db\column\FormNumber;

return new class() extends matrix\cli\Controller {

    protected function process($form) {
        $files = [];

        foreach (RESOURCE_FOLDERS as $folder) {
            $path = "{$folder}table";

            if (is_dir($path)) {
                $files = array_merge($files, scandir($path));
            }
        }

        foreach (array_unique($files) as $file) {
            $info = pathinfo($file);

            if (@$info['extension'] === 'php') {
                $table = table($info['filename']);

                foreach ($table->getColumns(false) as $column) {
                    if ($column instanceof FormNumber) {
                        db()->reset($column->sequence());
                    }
                }
            }
        }

        return ['success' => true];
    }

};
