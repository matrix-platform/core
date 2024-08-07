<?php //>

use matrix\db\column\CreateTime;
use matrix\db\column\CreatorAddress;
use matrix\db\column\Integer;
use matrix\db\column\Text;
use matrix\db\column\Textarea;
use matrix\db\Table;

$filter = function ($table, $conditions = []) {
    if (!defined('USER_ID') || USER_ID > 1) {
        $conditions[] = $table->id->greaterThan(1);
    }

    return $conditions;
};

$tbl = new Table('base_user_log', false);

$tbl->add('user_id', Integer::class)
    ->associate('user', 'User', false, $filter)
    ->readonly(true)
    ->required(true);

$tbl->add('type', Integer::class)
    ->options(load_options('user-log-type'))
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
