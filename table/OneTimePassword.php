<?php //>

use matrix\db\column\CreateTime;
use matrix\db\column\CreatorAddress;
use matrix\db\column\Integer;
use matrix\db\column\Text;
use matrix\db\column\Timestamp;
use matrix\db\Table;

$tbl = new Table('base_one_time_password');

$tbl->add('type', Integer::class)
    ->options(load_options('otp-type'))
    ->readonly(true)
    ->required(true);

$tbl->add('target', Text::class)
    ->readonly(true)
    ->required(true);

$tbl->add('password', Text::class)
    ->readonly(true)
    ->required(true);

$tbl->add('ip', CreatorAddress::class);

$tbl->add('verify_time', Timestamp::class);

$tbl->add('create_time', CreateTime::class);

$tbl->ranking('-id');

return $tbl;
