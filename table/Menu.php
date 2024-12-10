<?php //>

use matrix\db\column\DisableTime;
use matrix\db\column\EnableTime;
use matrix\db\column\Image;
use matrix\db\column\Integer;
use matrix\db\column\Ranking;
use matrix\db\column\Text;
use matrix\db\Table;

$tbl = new Table('base_menu');

$tbl->add('parent_id', Integer::class)
    ->associate('parent', 'Menu', true);

$tbl->add('title', Text::class)
    ->multilingual(MULTILINGUAL)
    ->required(true);

$tbl->add('subtitle', Text::class)
    ->multilingual(MULTILINGUAL);

$tbl->add('icon', cfg('style/table.Menu.icon') ?: Image::class);

$tbl->add('url', Text::class);

$tbl->add('enable_time', EnableTime::class);

$tbl->add('disable_time', DisableTime::class);

$tbl->add('ranking', Ranking::class);

$tbl->id->composite('item', 'Menu');

return $tbl;
