<?php //>

return function ($type, $id) {
    return load_data("permission/{$type}/{$id}");
};
