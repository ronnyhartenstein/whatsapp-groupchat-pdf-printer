<?php

class video {
  static $missing = [];

  static function fwrite($f, &$msg) {
    if ($msg['media_mime_type'] == 'video/mp4') {
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
    <video controls src="/' . $pic . '" alt="' . ($msg['media_caption'] ?: '') . '">
  </div>');
        }
        if (!empty($msg['media_caption'])) {
          fwrite($f, $msg['media_caption']);
        }
      }

    }
  }
}