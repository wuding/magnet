<?php
$id = 0;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    print_r($_GET);
    exit;
}

$max = 10000;
if (isset($_GET['max'])) {
    $max = $_GET['max'];
}

$timeout=1000;
if (isset($_GET['time'])) {
    $timeout = $_GET['time'];
}

$url = 'http://btmee.net/show/' . $id;
$str = file_get_contents($url);
//echo $str;
//exit;

//$filename = 'P:\websites\cache\http\btmee.net\show\\' . $id . '.htm';
$filename = '/home/fmcom/cache/btmee/' . $id . '.htm';

$handle = fopen($filename, 'w');
if ($handle) {
    $fwrite = fwrite($handle, $str);
    $closed = fclose($handle);
}

$next = $id + 1;

if ($next < $max) {
    ?>
    <script>
        setTimeout("window.location.href='?id=<?= $next ?>&max=<?= $max ?>&time=<?= $timeout ?>';",<?= $timeout ?>);
    </script>
    <?php
}
?>
