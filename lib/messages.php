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
  static $emoji = null;

  public function __construct() {
    if (is_null(self::$emoji)) {
      self::$emoji = new Emojione\Client();
      self::$emoji->shortcodes = false;
      //self::$emoji->spriteSize = '64';
      self::$emoji->unicodeAlt = false;
      self::$emoji->imagePathPNG = 'http://localhost:4000/assets/emojione/';
    }
  }

  public function fwrite_render($f, $msg, &$contacts, &$group_participants) {
    if (self::$dbg) fwrite(self::$dbg, "\n".str_repeat('-',80)."\n".var_export($msg, 1));
    $ts = intval($msg['received_timestamp'] / 1000);
    $data = $msg['data'];

    /* neuer Tag */
    if (self::$last_day != date('d.m.Y', $ts)) {
      fwrite($f, '
<div class="text-center">
  <div class="day">'.date('d.m.Y', $ts).'</div>
  <div class="age">'.self::timon_alter($ts).'</div>
</div>
<div class="clearfix"></div>');
    }

    $jid_remote = $msg['key_from_me'] ? 'me' : $msg['remote_resource'];

    fwrite($f, '
<div class="message pull-left'.($jid_remote != self::$last_jid ? ' new' : '').'">');
    //fwrite($f, '<div class="id">_id:'.$msg['_id'].'</div>');

    /* Sender */
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
      $text = self::$emoji->toImage($data);
      if (self::only_emoji($text)) {
        fwrite($f, '<span class="big">'.$data.'</span>');
      } else {
        fwrite($f, $text);
      }
    }

    bild::fwrite($f, $msg);
    video::fwrite($f, $msg);

    fwrite($f, '<span class="time">'.date('H:i', $ts).'</span>');

    fwrite($f, '
</div>
<div class="clearfix"></div>');
  
    self::$last_jid = $jid_remote;
    self::$last_day = date('d.m.Y', $ts);
  }

  static function only_emoji($text) {
    $emoji = 0;
    $text = preg_replace_callback(
      '/<img class="emojione".*?>/', 
      function($n) use (&$emoji) {
        $emoji++; 
        return ''; 
      }, 
      trim($text)
    );
    return empty($text) && $emoji >= 1 && $emoji <= 2;
  }

  static function timon_alter($ts) {
    $birth = new timonalter('September 20, 2016 3:40:00 PM');
    $current = new timonalter();
    $current->setTimestamp($ts);
    return $current->getRelativeDate($birth);
  }
}
