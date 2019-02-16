<?php
if(isset($_GET["json"]) && file_exists($_GET["json"])) {
    $str = file_get_contents($_GET["json"]);
    echo $str;
} else {
    echo json_encode(array("Error"=>"The path to the json file is incorrect. Check ?path in URL."));
}
?>