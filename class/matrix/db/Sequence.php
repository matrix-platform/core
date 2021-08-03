<?php //>

namespace matrix\db;

interface Sequence {

    function next($name);

    function reset($name);

}
