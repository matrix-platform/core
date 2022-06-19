<?php //>

model('MemberLog')->insert(['member_id' => $result['member_id'], 'type' => 4]);

require 'error.php';
