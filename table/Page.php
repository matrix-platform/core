<?php //>

use matrix\db\column\Boolean;
use matrix\db\column\Color;
use matrix\db\column\DisableTime;
use matrix\db\column\EnableTime;
use matrix\db\column\Text;
use matrix\db\column\Textarea;
use matrix\db\Table;

$tbl = new Table('base_page');

$tbl->add('path', Text::class)
    ->required(true)
    ->unique(true);

$tbl->add('title', Text::class)
    ->multilingual(MULTILINGUAL)
    ->required(true);

$tbl->add('description', Textarea::class)
    ->multilingual(MULTILINGUAL);

$tbl->add('fluid', Boolean::class)
    ->default(false)
    ->required(true)
    ->tab('style');

$tbl->add('color', Color::class)
    ->tab('style');

$tbl->add('bg_color', Color::class)
    ->tab('style');

$tbl->add('header', Boolean::class)
    ->default(true)
    ->options(load_options('visible'))
    ->required(true)
    ->tab('style');

$tbl->add('footer', Boolean::class)
    ->default(true)
    ->options(load_options('visible'))
    ->required(true)
    ->tab('style');

$tbl->add('enable_time', EnableTime::class);

$tbl->add('disable_time', DisableTime::class);

$tbl->ranking('path');

$tbl->id->composite('block', 'Block');

return $tbl;
