<?php //>

use matrix\db\column\CreateTime;
use matrix\db\column\CreatorAddress;
use matrix\db\column\Integer;
use matrix\db\column\Text;
use matrix\db\column\Textarea;
use matrix\db\Table;

$tbl = new Table('base_sms_log');

$tbl->add('receiver', Text::class)
    ->readonly(true)
    ->required(true);

$tbl->add('content', Text::class)
    ->readonly(true)
    ->required(true);

$tbl->add('response', Textarea::class)
    ->readonly(true);

$tbl->add('ip', CreatorAddress::class);

$tbl->add('create_time', CreateTime::class)
    ->required(true);

$tbl->add('status', Integer::class)
    ->default(0)
    ->invisible(true)
    ->options(load_options('sms-status'))
    ->required(true);

$tbl->ranking('-id');

return $tbl->exportable(true);
