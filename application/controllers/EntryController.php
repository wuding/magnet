<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EntryController
 *
 * @author Benli
 */
class EntryController {
    var $rewrite;
    function EntryController(){
        $this->init();
    }
    function init(){
        global $rewrite;
        $this->rewrite=$rewrite;
        $rewrite->action = 'indexAction';
    }

    function indexAction() {
        $id = $this->rewrite->pathinfo['filename'];
        
        include_once '../application/models/DbTable/UrlEntries.php';
        $UrlEntries = new DbTable_UrlEntries();
        $r=$UrlEntries->fetchRow("`id`=".$id);
        
         $where="";
        
        $q=NULL;
        $textUrl='';
        $text='';
      
        
        if(isset($_GET['q'])){
            $q=$_GET['q'];
            $q=trim($q);
            if($q){
                $where.="(";
                 $where.= "`title` LIKE '%" . $q."%'";
                     $where.=" OR ";
                     $where.="`intro` LIKE '%" . $q . "%'";
                     $where.=" OR ";
                     $where.="`magnet` LIKE '%" . $q . "%'";
                     $where.=" OR ";
                     $where.="`ed2k` LIKE '%" . $q . "%'";
                     $where.=")";
                     
                $textUrl=  rawurlencode($q);
                $text=htmlspecialchars($q);
            }
        }
        
         $where4=$where;
        if($where){
               $where=" AND ".$where;
        }

        $where2 = "id<" . $r->id.$where;
        $r2 = $UrlEntries->fetchRow($where2, 'id DESC');

        $where3 = "id>" . $r->id.$where;
        $r3 = $UrlEntries->fetchRow($where3, 'id ASC');
        
        $where5="";
        $where6=" WHERE id>" . $r->id;
        if($where4){
            $where5=" WHERE ".$where4;
            $where6.=" AND ".$where4;
        }
        
        $mysql_query= mysql_query("SELECT count(id) FROM `".$UrlEntries->_name."`".$where5, $UrlEntries->link_identifier);
        
        $mysql_fetch_object=  mysql_fetch_array($mysql_query);
//        print_r($mysql_fetch_object);exit;
        
        $total=$mysql_fetch_object['count(id)'];
        
        
//        $per=30;
        $page=1;
        if(isset($_GET['page'])){
            $page=$_GET['page'];
        }
        
//        $pages=ceil($total/$per);
        
        $sql="SELECT count(id) FROM `".$UrlEntries->_name."`".$where6." ORDER BY id DESC";
//        echo $sql;exit;
        $mysql_query2= mysql_query($sql, $UrlEntries->link_identifier);
        
        $mysql_fetch_object2=  mysql_fetch_array($mysql_query2);
//        print_r($mysql_fetch_object2);exit;
        
        $total2=$mysql_fetch_object2['count(id)'];
        
        
        $scriptFile='../application/views/scripts/entry/index.phtml';
        if (!$r) {
//            echo '404 Not Found';
            $scriptFile = '../application/views/scripts/error/error.phtml';
        }
        
        
        include_once $scriptFile;
        exit;
    }
}
$controller=new EntryController();
$controller->indexAction();
?>
