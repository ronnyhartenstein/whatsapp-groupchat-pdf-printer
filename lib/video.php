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
        $vid = $m[1];
        if (!file_exists('html/' . $vid)) {
          print 'x';
          self::$missing[] = $vid;
          fwrite($f, '
  <div class="yt_broken">[Video fehlt: ' . $vid . ']</div>');

        } else {
          $pic = self::thumbnail($vid);
          $vid_trg = 'videos/'.basename($vid);
          // umkopieren wg. Archivierung inkl. Thumbnail
          if (!file_exists('html/'.$vid_trg)) {
            $ok = copy('html/'.$vid, 'html/'.$vid_trg);
            print 'v'.($ok?'+':'-');
          }

          fwrite($f, '
  <div class="video thumbnail">
    <div class="play_icon"><div class="play"></div></div>
    '/*<video controls src="/' . $pic . '" preload="none">*/.'
    <img src="/'.$pic.'">
  </div>
  <span class="video-filename">'.basename($vid_trg).'</span>');
        }
        if (!empty($msg['media_caption'])) {
          fwrite($f, messages::$emoji->toImage($msg['media_caption']));
        }
      }
    }
  }

  static function thumbnail($vid) {
    if (!file_exists('html/videos')) mkdir('html/videos');
    $pic = preg_replace('/\.(mp4|3gp)$/', '.png', $vid);
    $trg = 'html/videos/'.basename($pic);
    if (!file_exists($trg)) {
      print 'v';
      $cmd = 'convert '.escapeshellarg('html/'.$vid).'[1] '.escapeshellarg($trg);
      exec($cmd);
      if (!file_exists($trg)) {
        print "\nError: Could not create video thumbail '$pic' from '$vid'\n";
        //die();
      }
    }
    return $pic;
  }
}