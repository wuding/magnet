<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SearchController
 *
 * @author Benli
 */
class SearchController {

    var $rewrite;

    function SearchController() {
        $this->init();
    }

    function init() {
        global $rewrite;
        $this->rewrite = $rewrite;
    }

    function indexAction() {
        
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
        
//        $time = time();
        
        $where="";
        $where2="";
        $q=NULL;
        $textUrl='';
        $text='';
        
        
        if(isset($_GET['q'])){
            $q=$_GET['q'];
            $q=trim($q);
            if($q){
                $where.= "`title` LIKE '%" . $q."%'";
                     $where.=" OR ";
                     $where.="`intro` LIKE '%" . $q . "%'";
                     $where.=" OR ";
                     $where.="`magnet` LIKE '%" . $q . "%'";
                     $where.=" OR ";
                     $where.="`ed2k` LIKE '%" . $q . "%'";

                     
                     
                 $textUrl=  rawurlencode($q);
                $text=htmlspecialchars($q);
            }
        }
        
        if($where){
            $where2=" WHERE ".$where;
        }
        
        $mysql_query= mysql_query("SELECT count(id) FROM `".$UrlEntries->_name."`".$where2, $UrlEntries->link_identifier);
        
        $mysql_fetch_object=  mysql_fetch_array($mysql_query);
//        print_r($mysql_fetch_object);exit;
        
        $total=$mysql_fetch_object['count(id)'];
        $pages=ceil($total/$per);
        
        
        $all=$UrlEntries->fetchAll($where,'id DESC',$limit.','.$per);
        $count=count($all);

//exit;
        $scriptFile = '../application/views/scripts/search/index.phtml';
        include_once $scriptFile;
    }

}

$controller = new SearchController();
$controller->indexAction();
?>
