<?php
require_once dirname(__FILE__)."/src/phpfreechat.class.php";
$params = array();
$params["title"] = "Chat de Tortuga";
$user=@$_GET['user'];
if(!empty($user))
$params["nick"] = $user;
else
$params["nick"] = 'invitado'.rand(1,1000);

$params['firstisadmin'] = true;
$params["serverid"] = md5(__FILE__); // calculate a unique id for this chat
$params["debug"] = false;
$params["language"]='es_ES';
$params["max_privmsg"]=20;
$params["theme"]="default";
$chat = new phpFreeChat( $params );


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>Chat Tortuga</title>
  <link rel="stylesheet" title="classic" type="text/css" href="style/generic.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/header.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/footer.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/menu.css" />
  <link rel="stylesheet" title="classic" type="text/css" href="style/content.css" />  
 </head>
 <body>



  <?php $chat->printChat(); ?>
  <?php if (isset($params["isadmin"]) && $params["isadmin"]) { ?>
    <p style="color:red;font-weight:bold;">Warning: because of "isadmin" parameter, everybody is admin. Please modify this script before using it on production servers !</p>
  <?php } ?>


</body></html>
