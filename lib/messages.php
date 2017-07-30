<?php

class messages {
  static function query($db, $id) {
    $res = $db->query('
      SELECT * FROM "main"."messages" 
      WHERE "key_remote_jid" = \''.$id.'\'
      ORDER BY "received_timestamp" ASC
    ');
    return $res;
  }
}