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

    public function remove($id) {
        unset($this->cache[$id]);
    }

}
