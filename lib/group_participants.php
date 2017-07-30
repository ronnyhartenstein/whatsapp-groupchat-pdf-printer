<?php

function get_group_participants($db, $group_jid, $contacts) {
  $colors = ['darkred','darkgreen','darkblue','purple','orange','olive', 'navy','teal','brown','darkcyan','darkslateblue'];
  $col_idx = 0;
  $res = $db->query('
    SELECT jid
    FROM "main"."group_participants" 
    WHERE "gjid" = \''.$group_jid.'\'
  ');
  $list = [];
  while ($v = $res->fetchArray()) {
    $list[$v['jid']] = [
      'color' => $colors[$col_idx],
      'name' => $contacts[$v['jid']],
    ];
    $col_idx++;
  }
  return $list;
}