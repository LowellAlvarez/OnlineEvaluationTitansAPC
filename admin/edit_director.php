<?php
include 'db_connect.php';
$qry = $conn->query("SELECT * FROM director_list where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
include 'new_director.php';
?>