<?php //>

namespace matrix\web;

trait Security {

    private function isLocked($table, $conditions, $type) {
        $seconds = cfg("security.{$type}-seconds");
        $count = cfg("security.{$type}-count");

        if ($seconds <= 0 || $count <= 0) {
            return false;
        }

        $conditions['type'] = 4;

        $timestamp = date(cfg('system.timestamp'), time() - $seconds);
        $conditions[] = $table->create_time->GreaterThan($timestamp);

        return $table->model()->count($conditions) >= $count;
    }

}
