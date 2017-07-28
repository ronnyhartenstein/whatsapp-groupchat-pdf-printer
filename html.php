<?php

$f = fopen('html/index.html','w');

fwrite($f, "
<html>
<head>
<title>Timon's Blog</title>
<!-- generiert ".date('d.m.Y H:i:s')." //-->
<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
<link href=\"/assets/bootstrap.min.css\" rel=\"stylesheet\">
<link href=\"/assets/styles.css\" rel=\"stylesheet\">
</head>

<body>
<div class=\"container\">
<h1>Timon's Blog</h1>
");

$db = new SQLite3('timonblog/msgstore.db');
$id = '4915159161040-1470252332@g.us';
$res = $db->query('
  SELECT * FROM "main"."messages" 
  WHERE "key_remote_jid" = \''.$id.'\' 
  ORDER BY "received_timestamp" ASC
');
while ($msg = $res->fetchArray()) {
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
    fwrite($f, "<br/>Bild: ".$msg['media_size']);
    if (!empty($msg['media_caption'])) {
      fwrite($f, " - ".$msg['media_caption']);
    }
  }
    //.(!empty($msg['media_name']) ? ' Bild '.$msg['media_name'] : '')
  /*return '
<div class="col-sm-6 col-md-4">
  <div class="thumbnail">
    <img src="'.$m[1].'" alt="'.($m[2] ?: '').'">
    '.(!empty($m[2]) ? $m[2] : '<div class="caption">'.$m[2].'</div>').'
  </div>
</div>';*/
}
$res->finalize();
$db->close();

fwrite($f, '
</body>
</html>');

fclose($f);
print "\nDone.";