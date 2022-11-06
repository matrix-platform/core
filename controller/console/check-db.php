<?php //>

// php www/index.php /console/check-db

return new class() extends matrix\cli\Controller {

    protected function process($form) {
        $files = [];

        foreach (RESOURCE_FOLDERS as $folder) {
            $path = $folder . 'table';

            if (is_dir($path)) {
                $files = array_merge($files, scandir($path));
            }
        }

        $files = array_unique($files);

        foreach ($files as $file) {
            $info = pathinfo($file);

            if (@$info['extension'] === 'php') {
                $table = table($info['filename']);

                foreach ($table->getColumns() as $column) {
                    if (!$this->defined($table->mapping(), $column->mapping())) {
                        echo HIR . "{$table->mapping()}.{$column->mapping()} NOT FOUND\n" . NOR;
                        continue;
                    }
                }
            }
        }

        return ['success' => true];
    }

    private function defined($table, $column) {
        $command = "
            SELECT COUNT(*) as \"count\"
              FROM information_schema.columns
             WHERE table_catalog = CURRENT_DATABASE()
               AND table_name = '{$table}'
               AND column_name = '{$column}'
        ";

        $statement = db()->prepare($command);
        $statement->execute();

        return intval($statement->fetchColumn());
    }

};
