<?php

class video {
  static $missing = [];

  static function hasMedia(&$msg) {
    return $msg['media_mime_type'] == 'video/mp4';
  }

  static function fwrite($f, &$msg) {
    if (self::hasMedia($msg)) {
      if (preg_match('#(Media/WhatsApp Video/.+?\.(mp4|3gp))#m', $msg[15], $m)) {
        //print "\n".$m[1];
        $pic = $m[1];
        if (!file_exists('html/' . $pic)) {
          print 'x';
          self::$missing[] = getcwd() . '/html/' . $pic;
          fwrite($f, '
  <div class="yt_broken">[Video fehlt: ' . $pic . ']</div>');
        } else {
          // raw_data hat ein Thumbnail!
          fwrite($f, '
  <div class="video thumbnail">
    <div class="play_icon"><div class="play"></div></div>
    <video controls src="/' . $pic . '" preload="none">
  </div>');
        }
        if (!empty($msg['media_caption'])) {
          fwrite($f, messages::$emoji->toImage($msg['media_caption']));
        }
      }

    }
  }
}