<?php //>

return function ($who) {
    return password_hash($who['password'], PASSWORD_DEFAULT);
};
