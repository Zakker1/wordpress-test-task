<?php 

$db_host = 'localhost';
$db_user = 'wp_user';
$db_pass = '111111Aa!';
$db_name = 'wp_db';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die('Ошибка подключения: ' . mysqli_connect_error());
}

function getCities() {
	global $conn;
	$citiesQuery = "select * from wp_city";
	$citiesResult = mysqli_query($conn, $citiesQuery);
	while ($row = $citiesResult->fetch_assoc()) {
	    $data[] = $row;
	}
	return $data;
}

function getWorks(){
	global $conn;
	$workQuery = "select * from wp_work";
	$workResult = mysqli_query($conn, $workQuery);
	while ($row = $workResult->fetch_assoc()) {
	    $workdata[] = $row;
	}
	return $workdata;
}

?>
