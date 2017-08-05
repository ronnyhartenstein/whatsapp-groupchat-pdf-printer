<?php

function pull_file($file) {
  $sdcard_dir = '/sdcard';
  $ret = 0;
  print "\npull '".$file."' .. ";
  exec('adb pull '.$sdcard_dir.'/'.$file ,$out, $ret);
  print ($ret == 1 ? "OK" : "Error");
}


print "\nPlease connect your Android device with USB Debugging enabled:\n";
exec('adb kill-server');
exec('adb start-server');
exec('adb wait-for-device');

$basepath = getcwd();

if (!file_exists('source')) {
    mkdir ('source');
}

chdir('source');
// ggf. /data/com.whatsapp/db
pull_file('WhatsApp/Databases/msgstore.db');
pull_file('WhatsApp/Databases/wa.db');
chdir($basepath);

$json_file = 'missing_media.json';
if (file_exists($json_file)) {
    $files = json_decode(file_get_contents($json_file));
    if (!empty($files) && is_array($files)) {
        print "\n".count($files)." media files to pull..";
        foreach ($files as $file) {
            $path = substr($file, 0, strrpos($file, '/'));
            chdir($path);
            pull_file($file);
            chdir($basepath);
        }
    }
}

print "\nDone.\n";