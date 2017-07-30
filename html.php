<?php
set_include_path(get_include_path().PATH_SEPARATOR.'lib');
spl_autoload_register();

$f = fopen('html/index.html','w');

header_footer::fwrite_header($f);

$db_wa = new SQLite3('source/wa.db');
$contacts = contacts::load($db_wa);
$db_wa->close();

$db_msgstore = new SQLite3('source/msgstore.db');
$jid_chatgroup = chat_group::get_jid_of_group_by_subject($db_msgstore, 'Timons Blog');
$group_participants = participants::get($db_msgstore, $jid_chatgroup, $contacts);
participants::fwrite_list($f, $group_participants);

$res_messages = messages::query($db_msgstore, $jid_chatgroup);

while ($msg = $res_messages->fetchArray()) {
  if (empty($msg['data']) 
    && (empty($msg['media_size'])
      || $msg['media_size'] < 100)
  ) {
    print "-";
    continue;
  }
  print ".";
  fwrite($f, "\n<p><strong>".date('d.m.Y H:i', $msg['received_timestamp'] / 1000)."</strong> ");
  //print " empfangen:".date('d.m.Y H:i', $msg['received_timestamp']);

  // remote_resource - Absender/Empfänger?

  $data = $msg['data'];
  if (!empty($data) && !is_numeric($data)) {
    fwrite($f, $data);
  }

  if ($msg['media_mime_type'] == 'image/jpeg') {
    print "*";
    if (preg_match('#(Media/WhatsApp Images/.+?\.jpg)#m', $msg[15], $m)) {
      //print "\n".$m[1];
      fwrite($f, '
<div class="row">
<div class="col-sm-6 col-md-4">
  <div class="thumbnail">
    <img src="/'.$m[1].'" alt="'.($msg['media_caption'] ?: '').'">
    '.(!empty($msg['media_caption']) ? '<div class="caption">'.$msg['media_caption'].'</div>' : '').'
  </div>
</div></div>');
    }
    /*if (!empty($msg['media_caption'])) {
      fwrite($f, " - ".$msg['media_caption']);
    }*/
  }
    //.(!empty($msg['media_name']) ? ' Bild '.$msg['media_name'] : '')

  //fwrite($f, '<xmp>'.var_export($msg, 1).'</xmp>');
}
$res_messages->finalize();
$db_msgstore->close();

header_footer::fwrite_footer($f);

fclose($f);
print "\nDone.";