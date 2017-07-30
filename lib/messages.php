<?php

class messages {
  static $dbg;

  static function dbg_on() {
    self::$dbg = fopen('messages_dump.log', 'w');
  } 

  static function query($db, $id) {
    $res = $db->query('
      SELECT * FROM "main"."messages" 
      WHERE "key_remote_jid" = \''.$id.'\'
      ORDER BY "received_timestamp" ASC
    ');
    return $res;
  }

  static $last_jid = '';
  static $last_day = '';

  static function fwrite_render($f, $msg, &$contacts, &$group_participants) {
    if (self::$dbg) fwrite(self::$dbg, "\n".str_repeat('-',80)."\n".var_export($msg, 1));
    $ts = intval($msg['received_timestamp'] / 1000);
    $data = $msg['data'];

    if (self::$last_day != date('d.m.Y', $ts)) {
      fwrite($f, '
<div class="text-center">
  <div class="day">'.date('d.m.Y', $ts).'</div>
</div>
<div class="clearfix"></div>');
    }
    fwrite($f, '
<div class="message pull-left'.($msg['remote_resource'] != self::$last_jid ? ' new' : '').'">');

    /* Sender */
    $jid_remote = $msg['key_from_me'] ? 'me' : $msg['remote_resource'];

    if ($jid_remote != self::$last_jid) {
      if (!empty($group_participants[$jid_remote])) {
        $p = $group_participants[$jid_remote];
        fwrite($f, '<div class="name" style="color: '.$p['color'].';">'.$p['name'].'</div>');
      } else {
        $p = $contacts[$jid_remote];
        fwrite($f, '<div class="name">'.$p.'</div>');
      }
    }

    /* Text */
    if (!empty($data) && !is_numeric($data)) {
      if (strlen($data) == 4 || strlen($data) == 8) { // Emoji
        fwrite($f, '<span class="big">'.$data.'</span>');
      } else {
        fwrite($f, $data);
      }
    }

    /* Bild */
    if ($msg['media_mime_type'] == 'image/jpeg') {
      print "*";
      if (preg_match('#(Media/WhatsApp Images/.+?\.jpg)#m', $msg[15], $m)) {
        //print "\n".$m[1];
        fwrite($f, '
  <div class="bild thumbnail">
    <img src="/'.$m[1].'" alt="'.($msg['media_caption'] ?: '').'">
  </div>');
        if (!empty($msg['media_caption'])) {
          fwrite($f, $msg['media_caption']);
        }
      }
    }
    fwrite($f, '<span class="time">'.date('H:i', $ts).'</span>');

    fwrite($f, '
</div>
<div class="clearfix"></div>');
  
    self::$last_jid = $jid_remote;
    self::$last_day = date('d.m.Y', $ts);
  }
}
