<?php
//---------- start of the script to detect DOS attact using snort ---------------
//---------- created by bruno hauck ---------------------------------------------
//---------- version 1.0 --------------------------------------------------------
//---------- date of creation 03/15/2015 ----------------------------------------
//---------- IDSgeo open source script ------------------------------------------

print_r("-------------------- The script is started -------------------\n\n");

$accountid = "1";
$tk = "KKKKKKKKKK";

require_once '/var/log/snort/db_connect.php';

$db = new DB_CONNECT();

print_r("------------- IDSgeo sisema de sensor -----------------\n");

print_r("------------- Created by bruno hauck ------------------\n");

//---------------------------- Making the sql script ------------------------
//$query_sql = "SELECT * FROM event";
//$query_sql = "SELECT * FROM event 
//INNER JOIN iphdr ON event.cid = iphdr.cid AND event.sid = iphdr.sid



//$query_sql ="SELECT event.cid, signature.sig_name FROM event
//INNER JOIN signature ON event.signature = signature.sig_id
//WHERE signature.sig_sid = 20000001";
//WHERE event.sid 20000001;

$query_sql = "SELECT COUNT(event.cid) as counter, signature.sig_id as signature_id, event.cid as event_id, signature.sig_name as signature_name 
		FROM event INNER JOIN signature ON event.signature = signature.sig_id";


$result = mysql_query($query_sql) or die(mysql_error());


print_r("------------------- Conected on snort db ---------------------\n\n");
$counting = 0;
if (mysql_num_rows($result) > 0){
	print_r("--------- the query found a result ---------------------\n\n");
	while($row = mysql_fetch_array($result)){
		//Values to be returned cid sid signature
                $counting++;
		print_r("\n- cid : - ".$row['event_id']);
		print_r("\n- counter : - ".$row['counter']);
		print_r("\n- sig_name : - ".$row['signature_name']);
		print_r("\n- sig_id : - ".$row['signature_id']);
                print_r("\n- Total: -".$counting);
	}
}
else{ print_r("------- nothing where found --------------"); }

if($counting>10){
	print_r("\n- A possible DOS Ataq -------------");
        print_r("\n- Generating the firewall rules to block the attack");
        print_r("\n- Starging the counter attack with kali linux");
        print_r("\n- Sending the alert to IDS and contacting the client about what is happaning");

	$url = 'http://www.softwareon.com.br/produto/ddos';

	$fields = array(
		'id'      => urlencode($accountid),
		'tk'      => urlencode($tk),
		'counter' => urlencode($counting),
		'ddos'    => urlencode('Y'),
		'msg'     => urlencode('A possible DOS atacq')
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
}
else{
	print_r("\n- Nothing is hapaning ----- the traffic is normal -----");
}
//weget 'http://server.com/auth?name=foo&password=bar';

?>
