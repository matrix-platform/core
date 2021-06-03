<?php //>

use matrix\db\column\Boolean;
use matrix\db\column\Date;
use matrix\db\column\Integer;
use matrix\db\column\Password;
use matrix\db\column\Text;
use matrix\db\Table;

$tbl = new Table('base_user');

$tbl->add('username', Text::class)
    ->pattern('^\w+$')
    ->required(true)
    ->unique(true)
    ->validation('regex');

$tbl->add('password', Password::class);

$tbl->add('group_id', Integer::class)
    ->associate('group', 'Group');

$tbl->add('begin_date', Date::class);

$tbl->add('expire_date', Date::class);

$tbl->add('disabled', Boolean::class)
    ->default(false)
    ->required(true);

$tbl->ranking('username');
$tbl->title('username');

return $tbl;
