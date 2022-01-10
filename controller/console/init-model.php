<?php //>

// php index.php /console/init-model table=custom_article [model=Article] [path=article] [debug]

return new class() extends matrix\cli\Controller {

    private $mapping = [
        'color' => 'color',
        'create_date' => 'createDate',
        'create_time' => 'createTime',
        'creator' => 'creator',
        'description' => 'textarea',
        'disable_time' => 'disableTime',
        'enable_time' => 'enableTime',
        'file' => 'file',
        'html' => 'html',
        'icon' => 'image',
        'image' => 'image',
        'ip' => 'creatorAddress',
        'mail' => 'email',
        'modified_date' => 'modifiedDate',
        'modified_time' => 'modifiedTime',
        'modifier' => 'modifier',
        'password' => 'password',
        'ranking' => 'ranking',
        'url' => 'url',
    ];

    protected function process($form) {
        $table = @$form['table'];

        if ($table === null) {
            return ['message' => 'invalid arguments'];
        }

        //--

        $model = @$form['model'];

        if ($model === null) {
            $tokens = explode('_', $table);
            $model = implode(array_map('ucfirst', array_splice($tokens, 1)));
        }

        //--

        $path = @$form['path'];

        if ($path === null) {
            $path = strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $model));
        }

        //--

        $metadata = $this->getDefinition($table);

        if (!$metadata) {
            return ['message' => "table `{$table}` not found"];
        }

        $types = [];

        foreach ($metadata as $data) {
            $types[$data['data_type']] = ucfirst(strtok($data['data_type'], ' '));
        }

        ksort($types);

        //-- table

        ob_start();

        lookup('console/model/table.twig')->render($this, [], [
            'table' => $table,
            'metadata' => $metadata,
            'types' => $types,
        ]);

        $this->save(APP_HOME . "table/{$model}.php", ob_get_clean(), @$form['debug']);

        //-- i18n

        foreach (LANGUAGES as $lang) {
            ob_start();

            lookup('console/model/i18n.twig')->render($this, [], [
                'metadata' => $metadata,
                'label' => load_i18n('label', $lang),
            ]);

            @mkdir(APP_HOME . "i18n/{$lang}/table", 0777, true);

            $this->save(APP_HOME . "i18n/{$lang}/table/{$model}.php", ob_get_clean(), @$form['debug']);
        }

        //-- list controller

        ob_start();

        lookup('console/model/list.twig')->render($this, [], [
            'model' => $model,
            'metadata' => $metadata,
        ]);

        @mkdir(APP_HOME . "controller/backend/{$path}", 0777, true);

        $this->save(APP_HOME . "controller/backend/{$path}.php", ob_get_clean(), @$form['debug']);

        //-- other controllers

        foreach (['content', 'delete', 'insert', 'new', 'update'] as $action) {
            ob_start();

            lookup("console/model/{$action}.twig")->render($this, [], ['model' => $model]);

            $this->save(APP_HOME . "controller/backend/{$path}/{$action}.php", ob_get_clean(), @$form['debug']);
        }

        //-- menu

        ob_start();

        lookup('console/model/menu.twig')->render($this, [], [
            'model' => $model,
            'path' => $path,
            'metadata' => $metadata,
        ]);

        echo ob_get_clean();

        //--

        return ['success' => true];
    }

    private function getDefinition($table) {
        $command = "SELECT column_name, data_type, is_nullable
                      FROM information_schema.columns
                     WHERE table_catalog = CURRENT_DATABASE()
                       AND table_name = '{$table}'
                       AND column_name <> 'id'
                       AND column_name NOT LIKE '%\\_\\_%'";

        $statement = db()->prepare($command);
        $statement->execute();

        $metadata = [];

        foreach ($statement->fetchAll() as $row) {
            $name = $row['column_name'];

            $row['is_nullable'] = ($row['is_nullable'] === 'YES');

            if (key_exists($name, $this->mapping)) {
                $row['data_type'] = $this->mapping[$name];
            } else {
                switch ($row['data_type']) {
                case 'bigint':
                    $row['data_type'] = 'integer';
                    break;
                case 'character varying':
                    $row['data_type'] = 'text';
                    break;
                }
            }

            $metadata[$name] = $row;
        }

        return $metadata;
    }

    private function save($file, $content, $debug) {
        if ($debug || file_exists($file) || !@file_put_contents($file, $content)) {
            echo "------------------------------------------------------------\n";
            echo $file . "\n";
            echo "============================================================\n";
            echo $content;
            echo "============================================================\n";
        }
    }

};
