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
        $time = time();
        $q = '';
        $text = '';
        $arr = array();
        $xt = '';
        $XT = array();
        $urn_type = '';
        $urn_hash = '';
        $all2 = array();

        include_once '../application/models/DbTable/UrnTypes.php';
        $UrnTypes = new DbTable_UrnTypes();

        include_once '../application/models/DbTable/UrlMagnet.php';
        $UrlMagnet = new DbTable_UrlMagnet();

        $all = $UrnTypes->fetchAll(NULL, NULL, NULL, "`id`,`name`");
//                            print_r($all);exit;
        $types = array();
        $typeIds = array();
        foreach ($all as $row) {
            $types[$row->name] = $row->id;
            $typeIds[$row->id] = $row->name;
        }
// print_r($typeIds);exit;        

        if (isset($_GET['q'])) {
            $q = $_GET['q'];
            $q = trim($q);
            if ($q) {
                $text = htmlspecialchars($q);

                if (preg_match('/^([\w]+):(.*)/i', $q, $matches)) {
// print_r($matches);
//                        exit;
                    $scheme = $matches[1];
                    $scheme = strtolower($scheme);
                    
                     $parse_url = parse_url($q);
//                        print_r($parse_url);
//                        exit;
                                            
                    if ('magnet' == $scheme) {


                        $query = $parse_url['query'];

//                        if(preg_match_all('/(&|)([\w]+)=([^&]+)/i', $query, $matches2)){
//                            print_r($matches2);
//                            exit;
//                            $KEY=$matches2[2];
//                            $VALUE=$matches2[3];
//                        }

                        $parse_str = parse_str($query, $arr);
//                        print_r($parse_str);
//                        print_r($arr);

                        if (isset($arr['xt'])) {
                            $xt = $arr['xt'];
                            $XT = explode(':', $xt);
//                            print_r($XT);
//                                exit;
                            $urn_type = $XT[1];
                            $urn_hash = $XT[2];
                            

                        }
                    } elseif ('urn' == $scheme) {
                        
                        $path=$parse_url['path'];
                        $PATH = explode(':', $path);
                        
                        $urn_type = $PATH[0];
                            $urn_hash = $PATH[1];
                        
                    }elseif(in_array($scheme, $typeIds)){ 
//                        echo $types[$scheme];exit;
                        $urn_type=$scheme;
                        
                        $urn_hash=$parse_url['path'];
                        
                    }else {
                        print_r($matches);
                        exit;
                    }
                    
                            if ('btih' == $urn_type) {
                                $where = "`type`=" . $types[$urn_type];
                                $where.=" AND ";
                                $where.="`hash` LIKE '" . $urn_hash . "'";

                                $all2 = $UrlMagnet->fetchAll($where, NULL, "0,1");
                                if (!$all2) {

                                    $dn = '';
                                    if (isset($arr['dn'])) {
                                        $dn = $arr['dn'];
                                    }

                                    $data = new stdClass();
                                    $data->type = 1;
                                    $data->hash = $urn_hash;
                                    $data->query = $query;
                                    $data->created = $time;
                                    $data->dn = $dn;
                                    $insertId = $UrlMagnet->insert($data);
                                } else {
//                                    print_r($all2);
//                                    exit;
                                }
                            } 
                }else{
                    $where = "`dn` LIKE '%" . $q."%'";
                     $where.=" OR ";
                     $where.="`hash` LIKE '%" . $q . "%'";

                     $all2 = $UrlMagnet->fetchAll($where, 'id DESC', "0,30");
                }
            }
        }
        

//exit;
        $scriptFile = '../application/views/scripts/search/index.phtml';
        include_once $scriptFile;
    }

}

$controller = new SearchController();
$controller->indexAction();
?>
