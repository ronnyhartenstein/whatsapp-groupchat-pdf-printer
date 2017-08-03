<?php
include 'vendor/autoload.php';

$f = fopen('html/index.html','w');

header_footer::fwrite_header($f);

$db_wa = new SQLite3('source/wa.db');
$contacts = contacts::load($db_wa);
$db_wa->close();

$db_msgstore = new SQLite3('source/msgstore.db');
$jid_chatgroup = chat_group::get_jid_of_group_by_subject($db_msgstore, 'Timons Blog');
$group_participants = participants::get($db_msgstore, $jid_chatgroup, $contacts);
//participants::fwrite_list($f, $group_participants);

$res_messages = messages::query($db_msgstore, $jid_chatgroup);
messages::dbg_on();
$last_jid = '';
$msg_proc = new messages();
while ($msg = $res_messages->fetchArray()) {
  if (empty($msg['data']) 
    && (empty($msg['media_size'])
      || $msg['media_size'] < 100)
  ) {
    print "-";
    continue;
  }
  print ".";
  if ($msg['media_mime_type'] == 'image/jpeg') {
    print "*";
  }

  $msg_proc->fwrite_render($f, $msg, $contacts, $group_participants);
}
$res_messages->finalize();
$db_msgstore->close();

header_footer::fwrite_footer($f);

fclose($f);

if (!empty(bild::$missing)) {
  print "\nBilder fehlen:\n\t" . implode("\n\t", bild::$missing);
}
if (!empty(video::$missing)) {
  print "\nVideos fehlen:\n\t" . implode("\n\t", video::$missing);
}

print "\nDone.\n";