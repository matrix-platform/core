<?php //>

namespace matrix\db;

class Cache {

    private $cache = [];

    public function get($id) {
        return @$this->cache[$id];
    }

    public function put($data) {
        $this->cache[$data['id']] = $data;
    }

    public function remove($id = null) {
        if ($id === null) {
            if ($this->cache) {
                $this->cache = [];
            }
        } else {
            unset($this->cache[$id]);
        }
    }

}
