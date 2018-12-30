<?php 

define('jsurlshort', true);
require_once('config.php');
require_once('alphaID.inc.php');

$returnFormat = (trim($_REQUEST['format'])); // json, plain
$longURL = (trim($_REQUEST['url'])); // http://google.de
$callback = $_REQUEST['callback']; // calback for json


@$result->status = true;
$result->longurl = $longURL;

// Error Checking
if(empty($longURL)) {
  $result->status = false;
  $result->text = "No URL given";
  returnError($result, $returnFormat);
  exit;
}

if(!validURL($longURL)) {
  $result->status = false;
  $result->text = "Wrong URL Format: ".$longURL;
  returnError($result, $returnFormat);
  exit;
}


$shortID = createShortURL($longURL);
$result->shortid = $shortID;
$result->shorturl = BASE_HREF.$shortID;


returnData($result, $returnFormat);


/* = Functions
 * ==========================================================*/
 
/**
* Get ShortURL ID
* @param {String} A Long URL to shorten
* @return {String}   Returns a string value containing the row id converted with alphaID function
*/ 
function createShortURL($longURL) {
	global $db;
  $id = checkURL($longURL);
  if(!$id){
	$sql = "INSERT INTO ".DB_TABLE." (longurl) VALUES (:longURL)";
	$prep = $db->prepare($sql);
	$prep->bindValue('longURL', $longURL);
	$prep->execute();
	$id = $db->lastInsertId();
	  }  
  $newShortID = alphaID($id);
  return $newShortID;
}

/**
* Output handling
* @param {Object} A Object containing data
* @param {String} Output Format plain or json
*/
function returnData($result, $returnFormat) {
  global $callback;
  if($returnFormat == "json"){
    if(!empty($callback)){
      echo $callback."(".json_encode($result).")";
    }else{
      echo json_encode($result);
    }    
  }else{
    echo $result->shorturl;
  }
}

/**
* Error Output handling
* @param {Object} A Object containing data
* @param {String} Output Format plain or json
*/
function returnError($error, $returnFormat) {
  global $callback;
  if($returnFormat == "json"){
    if(!empty($callback)){
      echo $callback."(".json_encode($error).")";  
    }else{
      echo json_encode($error);
    }
  }else{
    echo $error->text;
  }
}

/**
* Check URL for existing ShortURL
* @param {String} A Long URL to shorten
* @return {String|Bool} Returns a ID > 0 if a ShortURL exists or false if no ShortURL exists
*/
function checkURL($longURL) {
  global $db;
  
	$sql = "SELECT id FROM ".DB_TABLE." WHERE longurl=:longURL LIMIT 0,1";
	$prep = $db->prepare($sql);
	$prep->bindValue('longURL', $longURL);
	$prep->execute();
	$RowDATA = $prep->fetch();
	if($prep->rowCount() == 0){
		return false;
	}else{
		return $RowDATA["id"];
	}
}

/**
* Check for valid URL
* @param {String} A Long URL to shorten
* @return {Bool} Returns true if LongURL is valid
*/
function validURL($url) {
  $v = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
  return (bool)preg_match($v, $url);
}