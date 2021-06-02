<?php //>

namespace matrix\db;

interface Criterion {

    function bind($statement, $bindings);

    function make($dialect);

    function with($language);

}
