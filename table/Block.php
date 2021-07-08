<?php //>

use matrix\db\column\Color;
use matrix\db\column\DisableTime;
use matrix\db\column\EnableTime;
use matrix\db\column\Integer;
use matrix\db\column\Ranking;
use matrix\db\column\Text;
use matrix\db\Table;

$loader = function () {
    static $options;

    if ($options === null) {
        $names = explode('|', cfg('backend.modules'));
        $options = array_intersect_key(load_options('block-module'), array_flip($names));
    }

    return $options;
};

$tbl = new Table('base_block');

$tbl->add('page_id', Integer::class)
    ->associate('page', 'Page', true)
    ->readonly(true)
    ->required(true);

$tbl->add('module', Text::class)
    ->options($loader)
    ->readonly(true)
    ->required(true);

$tbl->add('title', Text::class)
    ->multilingual(MULTILINGUAL)
    ->required(true);

$tbl->add('content', Text::class)
    ->multilingual(MULTILINGUAL);

$tbl->add('image', Text::class);

$tbl->add('url', Text::class);

$tbl->add('extra', Text::class);

$tbl->add('padding_y', Integer::class)
    ->options(load_options('spacing'))
    ->required(true)
    ->tab('style');

$tbl->add('color', Color::class)
    ->tab('style');

$tbl->add('bg_color', Color::class)
    ->tab('style');

$tbl->add('enable_time', EnableTime::class);

$tbl->add('disable_time', DisableTime::class);

$tbl->add('ranking', Ranking::class);

$tbl->id->composite('item', 'BlockItem');

return $tbl;
