<?php //>

namespace matrix\db;

use PDO;

class Connection {

    private $boolean = false;
    private $caches = [];
    private $delegate;
    private $dialect;
    private $models = [];
    private $name;
    private $password;
    private $sequence;
    private $statements = [];
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

        if ($type === 'pgsql') {
            $this->boolean = true;
        }
    }

    public function begin() {
        $this->transaction = true;

        if ($this->delegate && !$this->delegate->inTransaction()) {
            $this->delegate->beginTransaction();

            foreach ($this->caches as $cache) {
                $cache->remove();
            }
        }
    }

    public function cacheable() {
        return $this->delegate && $this->delegate->inTransaction();
    }

    public function commit() {
        $this->transaction = false;

        if ($this->delegate && $this->delegate->inTransaction()) {
            $this->delegate->commit();
        }
    }

    public function createCache() {
        $cache = new Cache();

        $this->caches[] = $cache;

        return $cache;
    }

    public function dialect() {
        return $this->dialect;
    }

    public function lastId() {
        return $this->delegate ? $this->delegate->lastInsertId() : 0;
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

    public function prepare($command) {
        if (!$this->delegate) {
            $this->delegate = new PDO($this->name, $this->user, $this->password, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
            ]);

            if ($this->transaction) {
                $this->delegate->beginTransaction();
            }
        }

        if (!key_exists($command, $this->statements)) {
            $this->statements[$command] = $this->delegate->prepare($command);
        }

        return $this->statements[$command];
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

    public function supportedBooleanValue() {
        return $this->boolean;
    }

}
