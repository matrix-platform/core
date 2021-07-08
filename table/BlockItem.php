<?php //>

use matrix\db\column\DisableTime;
use matrix\db\column\EnableTime;
use matrix\db\column\Integer;
use matrix\db\column\Ranking;
use matrix\db\column\Text;
use matrix\db\Table;

$tbl = new Table('base_block_item');

$tbl->add('block_id', Integer::class)
    ->associate('block', 'Block', true)
    ->readonly(true)
    ->required(true);

$tbl->add('title', Text::class)
    ->multilingual(MULTILINGUAL);

$tbl->add('content', Text::class)
    ->multilingual(MULTILINGUAL);

$tbl->add('image', Text::class);

$tbl->add('url', Text::class);

$tbl->add('extra', Text::class);

$tbl->add('enable_time', EnableTime::class);

$tbl->add('disable_time', DisableTime::class);

$tbl->add('ranking', Ranking::class);

return $tbl;
