<?php

//---------- This file send the snort logs to the deskgeo server ---------------
//---------- created by bruno hauck ---------------------------------------------
//---------- version 1.0 --------------------------------------------------------
//---------- date of creation 12/03/2017 ----------------------------------------
//---------- IDSgeo open source script ------------------------------------------



//Accont ID from DESKgeo client
$accountid = "1";
//Account Token from DESKgeo client
$tk = "KKKKKKKKKK";

require_once '/var/log/snort/db_connect.php';

$db = new DB_CONNECT();



print_r("------------- Script start ------------------\n");



$query_sql = "SELECT COUNT( event.cid ) AS counter, event.sid, event.signature, iphdr.cid, iphdr.ip_src AS ip_src, iphdr.ip_dst AS ip_dst, signature.sig_name AS sig_name, signature.sig_id, signature.sig_sid, signature.sig_priority
FROM event
INNER JOIN iphdr ON event.cid = iphdr.cid
AND event.sid = iphdr.sid
INNER JOIN signature ON event.signature = signature.sig_id";

$result = mysql_query($query_sql) or die(mysql_error());


print_r("------------------- Conected on snort db ---------------------\n\n");

$counting = 0;
$signature = 0;
$sig_name;
$ip_source;
if (mysql_num_rows($result) > 0){
	print_r("--------- the query found a result ---------------------\n\n");
	while($row = mysql_fetch_array($result)){
		print_r("\n- cid : - ".$row['event_id']);
		print_r("\n- counter : - ".$row['counter']);
		print_r("\n- sig_name : - ".$row['sig_name']);
		print_r("\n- ip_source : - ".$row['ip_source']);
        $signature = $row['signature'];
        $sig_name  = $row['sig_name'];
        $ip_source = $row['ip_src'];
        $counting  = $row['counter'];
	}

}
else{ print_r("------- nothing where found --------------"); }

	$url = 'http://www.deskgeo.com/page/save_snort_log';

	$fields = array(
		'id'      => urlencode($accountid),
		'tk'      => urlencode($tk),
		'num_alerts' => urlencode($counting),
		'signature'    => urlencode($signature),
		'sig_name'    => urlencode($sig_name),
		'ip_source'     => urlencode($ip_source)
	);
	$postvars = "";
	foreach($fields as $key=>$value){
        	$postvars .= $key . "=" . $value . "&";
        }	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$response = curl_exec($ch);
	print_r("\n- curl response is - ".$response." ----- ");

//weget 'http://server.com/auth?name=foo&password=bar';


?>
