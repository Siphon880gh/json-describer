<?php
if(isset($_GET["desc"]) && file_exists($_GET["desc"])) {
    $str = file_get_contents($_GET["desc"]);
    echo $str;
} else {
    echo json_encode(array("Error"=>"The path to the description file is incorrect. It can be txt, html, or php. Check ?path in URL."));
}
?>