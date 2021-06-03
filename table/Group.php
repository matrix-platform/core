<?php //>

use matrix\db\column\Text;
use matrix\db\Table;

$tbl = new Table('base_group');

$tbl->add('title', Text::class)
    ->multilingual(MULTILINGUAL)
    ->required(true)
    ->unique(true);

$tbl->ranking('title');

return $tbl;
