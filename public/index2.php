<?php
//print_r($_SERVER);exit;
ini_set('display_errors',1);
error_reporting(E_ALL);
global $var,$rewrite,$db;
$var=new stdClass();
$var->includePaths=new stdClass();
$var->includePaths->library='../../../library/library.20130806';
require_once $var->includePaths->library.'/Wuphp.php';
require_once $var->includePaths->library.'/Wuphp/Rewrite.php';
$rewrite =new Wuphp_Rewrite();
require_once $var->includePaths->library.'/Wuphp/Db.php';
$db = new Wuphp_Db();
//$db->database_name='urlnk';
//$db->password='qeephp';
$db->username='fmcom_root';
$db->password='1325191712';
$db->database_name='fmcom_urlnk';
require_once $var->includePaths->library.'/Wuphp/Db/Table/Abstract.php';
if (!file_exists($rewrite->controllerFile)) {
    $rewrite->controllerFile='../application/views/scripts/error/error.phtml';
    print_r($rewrite);exit;
}
include_once $rewrite->controllerFile;
exit;
?>