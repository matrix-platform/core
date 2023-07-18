<?php //>

return new Twig\TwigFunction('query', function ($name, $filter = [], $size = 0) {
    $conditions = [];
    $table = table($name);

    foreach ($filter as $column => $value) {
        $column = @$table->{$column};

        if ($column) {
            if ($column->association() && $column->multiple()) {
                $conditions[] = $column->like("%{$value}%");
            } else {
                $conditions[] = is_array($value) ? $column->in($value) : $column->equal($value);
            }
        }
    }

    return $table->model()->query($conditions, true, $size);
});
