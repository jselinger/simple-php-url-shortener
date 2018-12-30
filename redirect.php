<?php 

define('jsurlshort', true);
require_once('config.php');
require_once('alphaID.inc.php');

$shortID = (trim($_REQUEST['id'])); 

$id = alphaID($shortID, true);

$sql = "SELECT longurl FROM ".DB_TABLE." WHERE id=:id LIMIT 0,1";
$prep = $db->prepare($sql);
$prep->bindValue('id', $id);
$prep->execute();
if($prep->rowCount() != 0){
  $RowDATA = $prep->fetch();
  $longURL = $RowDATA["longurl"];
  
    header('HTTP/1.1 302 Moved Temporarily');
    header('Location: ' .  $longURL);
  
 	$sql = "UPDATE ".DB_TABLE." SET hits=hits+1 WHERE id=:id";
	$prep = $db->prepare($sql);
	$prep->bindValue('id', $id);
	$prep->execute();
	
	$sql = "UPDATE ".DB_TABLE." SET lastused=NOW() WHERE id=:id";
	$prep = $db->prepare($sql);
	$prep->bindValue('id', $id);
	$prep->execute();

	//mysql_query("UPDATE ".DB_TABLE." SET referer='".$referer."' WHERE id='".$id."'");
  exit;
}else{
  return false;
}




