<?php //>

// php index.php /console/init-multilingual

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

        usort($files, function ($a, $b) {
            if (strpos($a, 'Common') === 0) {
                if (strpos($b, 'Common') !== 0) {
                    return -1;
                }
            } else {
                if (strpos($b, 'Common') === 0) {
                    return 1;
                }
            }

            return strcmp($a, $b);
        });

        //--

        foreach ($files as $file) {
            $info = pathinfo($file);

            if (@$info['extension'] === 'php') {
                $table = table($info['filename']);

                foreach ($table->getColumns() as $column) {
                    if (!$this->defined($table->mapping(), $column->mapping())) {
                        echo HIR . "{$table->mapping()}.{$column->mapping()} NOT FOUND\n" . NOR;
                        continue;
                    }

                    if ($column->multilingual()) {
                        foreach (LANGUAGES as $language) {
                            $this->add($table->mapping(), $column->mapping(), $language);
                        }
                    }
                }

                if ($table->versionable() && !$this->defined($table->mapping(), '__version__')) {
                    $command = "ALTER TABLE {$table->mapping()} ADD COLUMN __version__ INTEGER";

                    echo HIG . "{$command};\n" . NOR;

                    $statement = db()->prepare($command);
                    $statement->execute();

                    //--

                    $command = "UPDATE {$table->mapping()} SET __version__ = 1";

                    echo HIG . "{$command};\n" . NOR;

                    $statement = db()->prepare($command);
                    $statement->execute();

                }
            }
        }

        //--

        return ['success' => true];
    }

    private function add($table, $column, $language) {
        if ($this->defined($table, $column, $language)) {
            return;
        }

        $type = $this->type($table, $column);

        //--

        $command = "ALTER TABLE {$table} ADD COLUMN {$column}__{$language} {$type}";

        echo HIY . "{$command};\n" . NOR;

        $statement = db()->prepare($command);
        $statement->execute();

        //--

        $command = "UPDATE {$table} SET {$column}__{$language} = {$column}";

        echo HIY . "{$command};\n" . NOR;

        $statement = db()->prepare($command);
        $statement->execute();
    }

    private function defined($table, $column, $language = null) {
        if ($language) {
            $column = "{$column}__{$language}";
        }

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

    private function type($table, $column) {
        $command = "
            SELECT data_type
              FROM information_schema.columns
             WHERE table_catalog = CURRENT_DATABASE()
               AND table_name = '{$table}'
               AND column_name = '{$column}'
        ";

        $statement = db()->prepare($command);
        $statement->execute();

        return strtoupper($statement->fetchColumn());
    }

};
