<?php //>

use matrix\db\column\Boolean;
use matrix\db\column\Email;
use matrix\db\column\Password;
use matrix\db\column\Text;
use matrix\db\Table;

$tbl = new Table('base_member');

$tbl->add('username', Text::class)
    ->required(true)
    ->unique(true);

$tbl->add('password', Password::class);

$tbl->add('name', Text::class);

$tbl->add('mobile', Text::class);

$tbl->add('mail', Email::class);

$tbl->add('disabled', Boolean::class)
    ->default(false)
    ->required(true);

$tbl->ranking('username');
$tbl->title('username');

return $tbl;
