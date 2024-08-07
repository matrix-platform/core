<?php //>

use matrix\db\column\CreateTime;
use matrix\db\column\CreatorAddress;
use matrix\db\column\Integer;
use matrix\db\column\Text;
use matrix\db\column\Textarea;
use matrix\db\Table;

$tbl = new Table('base_member_log', false);

$tbl->add('member_id', Integer::class)
    ->associate('member', 'Member')
    ->readonly(true)
    ->required(true);

$tbl->add('type', Integer::class)
    ->options(load_options('member-log-type'))
    ->readonly(true)
    ->required(true);

$tbl->add('content', Textarea::class)
    ->readonly(true);

$tbl->add('ip', CreatorAddress::class);

$tbl->add('timestamp', Text::class)
    ->invisible(true)
    ->readonly(true);

$tbl->add('create_time', CreateTime::class);

$tbl->ranking('-id');

return $tbl->exportable(true);
