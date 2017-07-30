<?php

function get_jid_of_group_by_subject($db, $subject) {
  $res = $db->query('
    SELECT "key_remote_jid"
    FROM "main"."chat_list" 
    WHERE "subject" = \''.$subject.'\'
  ');
  $v = $res->fetchArray();
  return $v['key_remote_jid'];
}