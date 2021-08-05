<?php //>

namespace matrix\db;

use PDO;

class Connection {

    private $delegate;
    private $dialect;
    private $models = [];
    private $name;
    private $password;
    private $sequence;
    private $transaction;
    private $user;

    public function __construct($name, $user, $password) {
        $this->name = $name;
        $this->user = $user;
        $this->password = $password;

        $type = strstr($name, ':', true);
        $dialect = "matrix\\db\\{$type}\\Dialect";
        $sequence = "matrix\\db\\{$type}\\Sequence";

        $this->dialect = new $dialect();
        $this->sequence = new $sequence($this);
    }

    public function begin() {
        $this->transaction = true;

        if ($this->delegate && !$this->delegate->inTransaction()) {
            $this->delegate->beginTransaction();
        }
    }

    public function commit() {
        $this->transaction = false;

        if ($this->delegate && $this->delegate->inTransaction()) {
            $this->delegate->commit();
        }
    }

    public function dialect() {
        return $this->dialect;
    }

    public function model($table) {
        $name = $table->name();

        if (!key_exists($name, $this->models)) {
            $className = "{$table->namespace()}\\{$name}";

            if (class_exists($className)) {
                $this->models[$name] = new $className($this, $table);
            } else {
                $this->models[$name] = new Model($this, $table);
            }
        }

        return $this->models[$name];
    }

    public function next($name) {
        return $this->sequence->next($name);
    }

    public function prepare($statement) {
        if (!$this->delegate) {
            $this->delegate = new PDO($this->name, $this->user, $this->password, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
            ]);

            if ($this->transaction) {
                $this->delegate->beginTransaction();
            }
        }

        return $this->delegate->prepare($statement);
    }

    public function reset($name) {
        $this->sequence->reset($name);
    }

    public function rollback() {
        $this->transaction = false;

        if ($this->delegate && $this->delegate->inTransaction()) {
            $this->delegate->rollBack();
        }
    }

}
