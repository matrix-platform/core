<?php //>

use matrix\db\column\CreateTime;
use matrix\db\column\CreatorAddress;
use matrix\db\column\Html;
use matrix\db\column\Integer;
use matrix\db\column\Text;
use matrix\db\column\Timestamp;
use matrix\db\Table;

$tbl = new Table('base_mail_log');

$tbl->add('sender', Text::class)
    ->required(true);

$tbl->add('receiver', Text::class)
    ->readonly(true)
    ->required(true);

$tbl->add('subject', Text::class)
    ->readonly(true)
    ->required(true);

$tbl->add('content', Html::class)
    ->readonly(true)
    ->required(true);

$tbl->add('ip', CreatorAddress::class);

$tbl->add('create_time', CreateTime::class)
    ->required(true);

$tbl->add('send_time', Timestamp::class);

$tbl->add('status', Integer::class)
    ->default(0)
    ->options(load_options('mail-status'))
    ->required(true);

$tbl->ranking('-id');

return $tbl->exportable(true);
