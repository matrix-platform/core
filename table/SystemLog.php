<?php //>

use matrix\db\column\CreateTime;
use matrix\db\column\Integer;
use matrix\db\column\Textarea;
use matrix\db\Table;

$tbl = new Table('base_system_log');

$tbl->add('type', Integer::class)
    ->options(load_options('system-log-type'))
    ->readonly(true)
    ->required(true);

$tbl->add('content', Textarea::class)
    ->readonly(true);

$tbl->add('create_time', CreateTime::class)
    ->required(true);

$tbl->ranking('-id');

return $tbl->exportable(true);
