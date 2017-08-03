<?php

class header_footer {
  static function fwrite_header($f) {
    fwrite($f, "
<html>
<head>
<title>Timon's Blog</title>
<!-- generiert ".date('d.m.Y H:i:s')." //-->
<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
<link href=\"/assets/bootstrap.min.css\" rel=\"stylesheet\">
<link href=\"/assets/styles.css\" rel=\"stylesheet\">
<link href=\"/assets/print.css\" rel=\"stylesheet\" media=\"print\">
</head>

<body>
<div class=\"container\">
<h1>Timon's Blog</h1>
");
  }

  static function fwrite_footer($f) {
    fwrite($f, '
</body>
</html>');
  }
}