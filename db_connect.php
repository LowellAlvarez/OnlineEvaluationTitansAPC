<?php 
$server = "sql300.epizy.com";
$username = "epiz_31297178";
$password = "JuPR7429Sh";
$dbname = "epiz_31297178_titansapconlineevaluation";

$conn= mysqli_connect($server, $username, $password, $dbname);

$conn= new mysqli('localhost','root','','apc_test_db')or die("Could not connect to mysql".mysqli_error($con));
