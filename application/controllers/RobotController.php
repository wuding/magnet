<?php

class RobotController {

    var $rewrite;

    function RobotController() {
        $this->init();
    }

    function init() {
        global $rewrite;
        $this->rewrite = $rewrite;
    }

    function indexAction() {
        $entryId=0;
        if(isset($_GET['id'])){
            $entryId=$_GET['id'];
        }else{
            print_r($_GET);
            exit;
        }
        $next = $entryId + 1;
        
        $max = 10000;
if (isset($_GET['max'])) {
    $max = $_GET['max'];
}

$timeout=3000;
if (isset($_GET['time'])) {
    $timeout = $_GET['time'];
}
        
        $time=time();
        include_once '../application/models/DbTable/UrlClasses.php';
        $UrlClasses = new DbTable_UrlClasses();
        
        include_once '../application/models/DbTable/UrlEntries.php';
        $UrlEntries = new DbTable_UrlEntries();
        
        $data = new stdClass();
        $data->siteId=1;
        $data->entry=$entryId;
        
        $ed2k=array();
        $img=array();
        
                
//        $filename = 'P:\websites\cache\http\btmee.net\show\\'.$data->entry.'.htm';
        $filename = '/home/fmcom/cache/btmee/' . $data->entry . '.htm';
        
        $doc = new DOMDocument();
        @$doc->loadHTMLFile($filename);
        $div_NodeList = $doc->getElementsByTagName('div');
//print_r($div_NodeList->length);exit;
        if(!$div_NodeList->length){
            $url='btmee.php?id='.$entryId;
            echo '<a href="'.$url.'">'.$url.'</a>';
            exit;
        }
        for ($i = 0; $i < $div_NodeList->length; $i++) {
            $div_Element = $div_NodeList->item($i);
//    print_r($div_Element);
//    exit;
            $div_NodeMap = $div_Element->attributes;

            $class_Attr = $div_NodeMap->getNamedItem('class');
            if ($class_Attr) {
                $class = $class_Attr->value;

                if ('title' == $class) {
                    $a_NodeList = $div_Element->getElementsByTagName('a');
                    $sup=-1;
                    for ($j = 1; $j < $a_NodeList->length; $j++) {
                        $a_Element = $a_NodeList->item($j);
                        $Class = new stdClass();
                        $Class->sup = $sup;
                        $Class->siteId = 1;
                        $Class->title=$a_Element->nodeValue;                        
                        $a_NodeMap = $a_Element->attributes;
                        $href_Attr = $a_NodeMap->getNamedItem('href');
                        if ($href_Attr) {
                            $href = $href_Attr->value;
                            $href=trim($href,'/');
                            $Class->class=$href;
                            $row=$UrlClasses->fetchRow("siteId=1 AND class='".$href."'");
                            if(!$row){
                                $insertId=$UrlClasses->insert($Class);
                                $sup=$insertId;
                            }else{
                                $sup=$row->id;
                            }
                        }
                    }
                    $data->classId=$sup;
                    
                    $span_NodeList = $div_Element->getElementsByTagName('span');
                    $span_Element = $span_NodeList->item(0);
                    $span_nodeValue=$span_Element->nodeValue;
                    if(preg_match('/(.*) 由 (.*) 发布(.*)/', $span_nodeValue, $matches)){
                         
                         $modified=$matches[1];
                         $data->modified=strtotime($modified);
                         $data->user=$matches[2];
                    }
                   
                
                }
            }
        }
        
        $td_NodeList = $doc->getElementsByTagName('td');
        for ($k = 0; $k < $td_NodeList->length; $k++) {
            $td_Element = $td_NodeList->item($k);
            $td_NodeMap = $td_Element->attributes;

            $class_Attr2 = $td_NodeMap->getNamedItem('class');
            if ($class_Attr2) {
                $class2 = $class_Attr2->value;

                if ('name' == $class2) {
                    $h1_NodeList = $td_Element->getElementsByTagName('h1');
                    $h1_Element = $h1_NodeList->item(0);
                    $h1_nodeValue=$h1_Element->nodeValue;
                    $data->title=$h1_nodeValue;
                    
                    $em_NodeList = $td_Element->getElementsByTagName('em');
                    $em_Element = $em_NodeList->item(0);
                    $em_nodeValue=$em_Element->nodeValue;
//                    echo $em_nodeValue;exit;
                    if(preg_match('/([0-9\.]+)(\s+[a-z]+|[a-z]+|)/i', $em_nodeValue, $matches2)){ 
//                        print_r($matches2);exit;
                        $size=$matches2[1];
                        $uni=1; 
                        $unit=  $matches2[2];
                        $unit=trim($unit);
                        if($unit){
                            $unit=  strtoupper($unit);
                            if('MB'==$unit || 'M'==$unit){
                                $uni=1024*1024;
                            }elseif('GB'==$unit || 'G'==$unit){
                                $uni=1024*1024*1024;
                            }elseif('KB'==$unit || 'K'==$unit){
                                $uni=1024;
                            }else{
                                print_r($matches2);exit;
                            }
                        
                        }elseif(30>$size){
                            $uni=1024*1024*1024;
                        }elseif(30<$size){
                            $uni=1024*1024;
                        }else{
                                print_r($matches2);exit;
                            }
                        
                        $size=$size*$uni;
                        $data->size=$size;
                    }
//                    else{
//                        print_r($matches2);exit;
//                    }
                    
                }elseif ('ed2k' == $class2) {
                    $a_NodeList2 = $td_Element->getElementsByTagName('a');
                   
                    for ($l = 0; $l < $a_NodeList2->length; $l++) {
                        $a_Element2 = $a_NodeList2->item($l);
                        $a_NodeMap2 = $a_Element2->attributes;
                        $href_Attr2 = $a_NodeMap2->getNamedItem('href');
                        if ($href_Attr2) {
                            $href2 = $href_Attr2->value;
                            $ed2k[]=$href2;
                        }
                    }
                }elseif('description'==$class2){
                     $pre_NodeList = $td_Element->getElementsByTagName('pre');
                     
                    $pre_Element = $pre_NodeList->item(0);
                    if($pre_Element){
                    $img_NodeList = $pre_Element->getElementsByTagName('img');
        for ($l = 0; $l < $img_NodeList->length; $l++) {
            $img_Element = $img_NodeList->item($l);
            $img_NodeMap = $img_Element->attributes;

            $src_Attr = $img_NodeMap->getNamedItem('src');
            if ($src_Attr) {
                $src = $src_Attr->value;
                $img[]=$src;
            }
        }
                    $pre_nodeValue=$pre_Element->nodeValue;
                    $data->intro=strip_tags($pre_nodeValue);
//                    print_r($data);exit;
                    }
                }
            }
        }
        
        $copylink1=$doc->getElementById('copylink1');
        if($copylink1){
            $data->magnet=$copylink1->nodeValue;
        }
        
        if($ed2k){
            $data->ed2k=implode('
', $ed2k);
        }
        if($img){
            $data->img=implode('
', $img);
        }
        
        $row2=$UrlEntries->fetchRow("`siteID`=".$data->siteId." AND `entry`='".$data->entry."'");
        if(!$row2){
            $data->created=$time;
            $insertId2=$UrlEntries->insert($data);
        }else{
            if($row2->title != $data->title || $row2->magnet != $data->magnet){
//                print_r($data);exit;
                
                $updated=$UrlEntries->update($data,"id=".$row2->id);
            }
        }
        
        if ($next < $max) {
    ?>
    <script>
        setTimeout("window.location.href='?id=<?= $next ?>&max=<?= $max ?>&time=<?= $timeout ?>';",<?= $timeout ?>);
    </script>
    <?php
}
    }

}

$controller = new RobotController();
$controller->indexAction();

?>
