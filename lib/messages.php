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

  public static $missing_imgs = [];
  public static $missing_vids = [];

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

    /* Bild */
    if ($msg['media_mime_type'] == 'image/jpeg' || preg_match('/\.jpg$/', $msg['media_name'])) {
      print "*";
      if (preg_match('#(Media/WhatsApp Images/.+?\.jpg)#m', $msg[15], $m)) {
        //print "\n".$m[1];
        $pic = $m[1];
        if (!file_exists('html/'.$pic)) {
          print 'x';
          self::$missing_imgs[] = getcwd().'/html/'.$pic;
          fwrite($f, '
  <div class="image_broken">[Bild fehlt: '.$pic.']</div>');
        } else {
          fwrite($f, '
  <div class="bild thumbnail">
    <img src="/'.$pic.'" alt="'.($msg['media_caption'] ?: '').'">
  </div>');
        }
        if (!empty($msg['media_caption'])) {
          fwrite($f, $msg['media_caption']);
        }
      }
    }

    /* Video */
    if ($msg['media_mime_type'] == 'video/mp4') {
      if (preg_match('#(Media/WhatsApp Video/.+?\.(mp4|3gp))#m', $msg[15], $m)) {
        //print "\n".$m[1];
        $pic = $m[1];
        if (!file_exists('html/' . $pic)) {
          print 'x';
          self::$missing_vids[] = getcwd() . '/html/' . $pic;
          fwrite($f, '
  <div class="yt_broken">[Video fehlt: ' . $pic . ']</div>');
        } else {
          // raw_data hat ein Thumbnail!
          fwrite($f, '
  <div class="video thumbnail">
    <video controls src="/' . $pic . '" alt="' . ($msg['media_caption'] ?: '') . '">
  </div>');
        }
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
