<?php

function load_contacts($db) {
  $res = $db->query('
    SELECT jid, raw_contact_id, display_name
    FROM "main"."wa_contacts" 
    WHERE "is_whatsapp_user" = 1
  ');
  $list = [];
  while ($v = $res->fetchArray()) {
    $list[$v['jid']] = $v['display_name'];
  }
  return $list;
}