<?php

function query_messages($db, $id) {

  /*
  $res = $db->query('
    SELECT *, rowid "NAVICAT_ROWID" FROM "main"."message_thumbnails" 
    WHERE "key_remote_jid" = \''.$id.'\'
  ');
  $imgs = [];
  while ($img = $res->fetchArray()) {
    $imgs[$img['key_id']] = 
  }
  */

  $res = $db->query('
    SELECT * FROM "main"."messages" 
    WHERE "key_remote_jid" = \''.$id.'\'
    ORDER BY "received_timestamp" ASC
  ');
  }

  return $res;
}