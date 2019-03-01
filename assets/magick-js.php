<?php
  ob_start("ob_gzhandler");
  header("Cache-Control: public");
  header("Cache-Control: max-age=604800");
  header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
  header('Expires: ' . gmdate('D, d M Y H:i:s',time()+604800) . ' GMT');
  header("Content-type: text/javascript; charset=utf-8");
  readfile('jquery-3.3.1.slim.min.js');
  readfile('popper.min.js');
  readfile('bootstrap.min.js');
  ob_end_flush();
?>
