<?php
session_start();
if(!isset($_SESSION['png'])){
	exit();
}
//$timestamp_ary = $_SESSION['timestamp_ary']
//$gsh = new GlowStick_Histogram();
//$gsh->setDataAry($timestamp_ary, intval(10));
header('Content-Type: image/png');
echo $_SESSION['png'];

?>
