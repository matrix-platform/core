<?php //>

use matrix\db\column\CreateTime;
use matrix\db\column\Integer;
use matrix\db\column\ModifiedTime;
use matrix\db\column\ModifierAddress;
use matrix\db\column\Text;
use matrix\db\column\Timestamp;
use matrix\db\Table;

$tbl = new Table('base_auth_token');

$tbl->add('token', Text::class)
    ->readonly(true)
    ->required(true)
    ->unique(true);

$tbl->add('type', Integer::class)
    ->options(load_options('auth-token-type'))
    ->readonly(true)
    ->required(true);

$tbl->add('target_id', Integer::class)
    ->readonly(true)
    ->required(true);

$tbl->add('user_agent', Text::class);

$tbl->add('ip', ModifierAddress::class);

$tbl->add('modify_time', ModifiedTime::class);

$tbl->add('create_time', CreateTime::class);

$tbl->add('expire_time', Timestamp::class);

$tbl->ranking('-modify_time');

return $tbl;
