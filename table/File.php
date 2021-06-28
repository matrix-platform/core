<?php //>

use matrix\db\column\Boolean;
use matrix\db\column\Creator;
use matrix\db\column\Integer;
use matrix\db\column\ModifiedTime;
use matrix\db\column\Text;
use matrix\db\column\Textarea;
use matrix\db\Table;

$tbl = new Table('base_file');

$tbl->add('parent_id', Integer::class);

$tbl->add('type', Integer::class)
    ->readonly(true)
    ->required(true);

$tbl->add('name', Text::class)
    ->required(true);

$tbl->add('path', Text::class)
    ->readonly(true)
    ->unique(true);

$tbl->add('size', Integer::class)
    ->readonly(true);

$tbl->add('hash', Text::class)
    ->readonly(true);

$tbl->add('description', Textarea::class);

$tbl->add('mime_type', Text::class)
    ->readonly(true);

$tbl->add('width', Integer::class)
    ->readonly(true);

$tbl->add('height', Integer::class)
    ->readonly(true);

$tbl->add('seconds', Integer::class)
    ->readonly(true);

$tbl->add('privilege', Integer::class)
    ->required(true);

$tbl->add('owner_id', Creator::class)
    ->required(true);

$tbl->add('group_id', Integer::class);

$tbl->add('modified_time', ModifiedTime::class)
    ->required(true);

$tbl->add('deleted', Boolean::class)
    ->default(false)
    ->required(true);

$tbl->title('name');

return $tbl;
