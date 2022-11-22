<?php //>

return function ($who, $password) {
    return password_verify($password, $who['password']);
};
