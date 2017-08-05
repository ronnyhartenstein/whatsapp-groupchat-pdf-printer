<?php

class bild {
  static $missing = [];

  static function hasMedia(&$msg) {
    return $msg['media_mime_type'] == 'image/jpeg' || preg_match('/\.jpg$/', $msg['media_name']);
  }

  static function fwrite($f, &$msg) {
    if (self::hasMedia($msg)) {
      print "*";
      if (preg_match('#(Media/WhatsApp Images/.+?\.jpg)#m', $msg[15], $m)) {
        //print "\n".$m[1];
        $pic = $m[1];
        if (!file_exists('html/'.$pic)) {
          print 'x';
          self::$missing[] = $pic;
          fwrite($f, '
  <div class="image_broken">[Bild fehlt: '.$pic.']</div>');
        } else {
          fwrite($f, '
  <div class="bild thumbnail">
    <img src="/'.$pic.'">
  </div>');
        }
        if (!empty($msg['media_caption'])) {
          fwrite($f, messages::$emoji->toImage($msg['media_caption']));
        }
      }
    }
  }
}