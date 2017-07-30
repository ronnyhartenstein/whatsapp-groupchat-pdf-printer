<?php

class participants {
  
  static function get($db, $group_jid, $contacts) {
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
        'name' => !empty($contacts[$v['jid']]) ? $contacts[$v['jid']] : $v['jid'],
      ];
      $col_idx++;
    }
    return $list;
  }
  
  static function fwrite_list($f, $list) {
    fwrite($f, 'Mitwirkende:<ul>');
    $fn = function($v) use ($f) {
      fwrite($f, "\n".'<li><span color="'.$v['color'].'">'.$v['name'].'</span></li>');
    };
    array_map($fn, $list);
    fwrite($f, '</ul>');
  }
}