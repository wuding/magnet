<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of IndexController
 *
 * @author Benli
 */
class IndexController {
    var $rewrite;
    function IndexController(){
        $this->init();
    }
    function init(){
        global $rewrite;
        $this->rewrite=$rewrite;
    }
    function indexAction(){
        $per=30;
        
        $min=0;
        $max=$per+1;
        
        $limit=0;
        
        
        $page=1;
        $prevPage=0;
        $nextPage=2;
        if(isset($_GET['page'])){
            $page=$_GET['page'];
            $nextPage=$page+1;
            $prevPage=$page-1;
            
            $min=$per*$prevPage;
            $max=$min+$max;
            
            $limit=$per*$prevPage;
        }
        
        include_once '../application/models/DbTable/UrlEntries.php';
        $UrlEntries = new DbTable_UrlEntries();
        
        $mysql_query= mysql_query("SELECT count(id) FROM `".$UrlEntries->_name."`", $UrlEntries->link_identifier);
        
        $mysql_fetch_object=  mysql_fetch_array($mysql_query);
//        print_r($mysql_fetch_object);exit;
        
        $total=$mysql_fetch_object['count(id)'];
        $pages=ceil($total/$per);
        
        
//        $where=$min."<id && id<".$max;
        $where="`magnet` IS NOT NULL";
        $all=$UrlEntries->fetchAll($where,'id DESC',$limit.','.$per);
        $count=count($all);
        
        
        $scriptFile='../application/views/scripts/index/index.phtml';
        include_once $scriptFile;
        exit;
    }
}
$controller=new IndexController();
$controller->indexAction();
?>
