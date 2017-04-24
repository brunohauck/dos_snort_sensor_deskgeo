<?
// Snort Report 1.3.4
// Copyright (C) 2000-2013 Symmetrix Technologies, LLC.
// September 9, 2013
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
//
//


$type = $_GET['type'];
$ipAddress = $_GET['ipAddress'];
$beginTime = $_GET['beginTime'];
$endTime = $_GET['endTime'];

// strip out the bad stuff
$type = ereg_replace("[^A-Za-z]", "", $type);
$ipAddress = ereg_replace("[^0-9]", "", $ipAddress);
$beginTime = ereg_replace("[^0-9]", "", $beginTime);
$endTime = ereg_replace("[^0-9]", "", $endTime);
if ($ipAddress == "") {$ipAddress="0";}

require_once("functions.php");

$type == "src" ? $txt = "Source" : $txt = "Destination";
$title = sprintf("SNORT Report - Signatures for %s as a %s",
					dec2IP($ipAddress), $txt);

require_once("page_top.php");

$beginTime = intval($beginTime);
$endTime = intval($endTime);

$earliestAlert = "2010-01-01 12:00:00";		//set up earliest and latest alerts
$latestAlert = "1990-01-01 12:00:00";

// set up the SQL query
if ($type == "src") {
	$query = "SELECT event.cid, event.sid, event.signature, event.timestamp, iphdr.ip_src, iphdr.ip_dst, iphdr.ip_ver, iphdr.ip_hlen, iphdr.ip_tos, iphdr.ip_len, iphdr.ip_id, iphdr.ip_flags, iphdr.ip_off, iphdr.ip_ttl, iphdr.ip_proto, iphdr.ip_csum, signature.sig_name, signature.sig_id, signature.sig_sid FROM event, iphdr, signature WHERE event.cid = iphdr.cid AND event.sid = iphdr.sid AND event.signature = signature.sig_id AND iphdr.ip_src = ".$ipAddress." AND event.timestamp > ".$db->timestamp($beginTime)." AND event.timestamp < ".$db->timestamp($endTime) or die("Error in query");
} else {
	$query = "SELECT event.cid, event.sid, event.signature, event.timestamp, iphdr.ip_src, iphdr.ip_dst, iphdr.ip_ver, iphdr.ip_hlen, iphdr.ip_tos, iphdr.ip_len, iphdr.ip_id, iphdr.ip_flags, iphdr.ip_off, iphdr.ip_ttl, iphdr.ip_proto, iphdr.ip_csum, signature.sig_name, signature.sig_id, signature.sig_sid FROM event, iphdr, signature WHERE event.cid = iphdr.cid AND event.sid = iphdr.sid AND event.signature = signature.sig_id AND iphdr.ip_dst = ".$ipAddress." AND event.timestamp > ".$db->timestamp($beginTime)." AND event.timestamp < ".$db->timestamp($endTime) or die("Error in query");
}

// strip out the bad stuff
$query = ereg_replace("[^A-Za-z0-9\(\)\.\_\,\ \>\<\=]", "", $query);

// run the query on the database
$result = $db->query($query);

// iterate through the result set and construct array of events
$numOfEvents = 0;
while($myrow =  $db->farray($result))
{
	// Check for earliest and latest alerts
	if ($myrow["timestamp"] < $earliestAlert) $earliestAlert = $myrow["timestamp"]; 
	if ($myrow["timestamp"] > $latestAlert) $latestAlert = $myrow["timestamp"];

	// set common variables in array
	$a[$numOfEvents]["cid"] = $myrow["cid"];
	$a[$numOfEvents]["sid"] = $myrow["sid"];
	$a[$numOfEvents]["sig_id"] = $myrow["sig_id"];
	$a[$numOfEvents]["sig_sid"] = $myrow["sig_sid"];
	$a[$numOfEvents]["signature"] = $myrow["sig_name"];
	$a[$numOfEvents]["timestamp"] = $myrow["timestamp"];
	$a[$numOfEvents]["srcIP"] = $myrow["ip_src"];
	$a[$numOfEvents]["dstIP"] = $myrow["ip_dst"];
	$a[$numOfEvents]["verIP"] = $myrow["ip_ver"];
	$a[$numOfEvents]["hlenIP"] = $myrow["ip_hlen"];
	$a[$numOfEvents]["tosIP"] = $myrow["ip_tos"];
	$a[$numOfEvents]["lenIP"] = $myrow["ip_len"];
	$a[$numOfEvents]["idIP"] = $myrow["ip_id"];
	$a[$numOfEvents]["flagsIP"] = $myrow["ip_flags"];
	$a[$numOfEvents]["offIP"] = $myrow["ip_off"];
	$a[$numOfEvents]["ttlIP"] = $myrow["ip_ttl"];
	$a[$numOfEvents]["protoIP"] = $myrow["ip_proto"];
	$a[$numOfEvents]["csumIP"] = $myrow["ip_csum"];
	$b["signature"][$a[$numOfEvents][signature]]++;
	$b["sigid"][$a[$numOfEvents][signature]] = $myrow["sig_id"];
	$b["sigsid"][$a[$numOfEvents][signature]] = $myrow["sig_sid"];

// pull payload from data table
	$querydata = "SELECT data.data_payload FROM data WHERE data.cid = ".$myrow["cid"]." AND data.sid = ".$myrow["sid"];
	$resultdata = $db->query($querydata);
	$myrowdata =  $db->farray($resultdata);
	$a[$numOfEvents]["payload"] = $myrowdata["data_payload"];
	$db->free($resultdata);

// set unique variables in array
	switch ($myrow["ip_proto"]) {
		case 1:							//ICMP
			$queryicmp = "SELECT icmphdr.icmp_type, icmphdr.icmp_code, icmphdr.icmp_csum, icmphdr.icmp_id, icmphdr.icmp_seq FROM icmphdr WHERE icmphdr.cid = ".$myrow["cid"]." AND icmphdr.sid = ".$myrow["sid"] or die("Error in query");
			$resulticmp = $db->query($queryicmp);
			$myrowicmp =  $db->farray($resulticmp);
			$a[$numOfEvents]["typeICMP"] = $myrowicmp["icmp_type"];
			$a[$numOfEvents]["codeICMP"] = $myrowicmp["icmp_code"];
			$a[$numOfEvents]["csumICMP"] = $myrowicmp["icmp_csum"];
			$a[$numOfEvents]["idICMP"] = empty($myrowicmp["icmp_id"]) ? "" : $myrowicmp["icmp_id"];			
			$a[$numOfEvents]["seqICMP"] = empty($myrowicmp["icmp_seq"]) ? "" :$myrowicmp["icmp_seq"];
			$db->free($resulticmp);
			break;
		case 6:							//TCP
			$querytcp = "SELECT tcphdr.tcp_sport, tcphdr.tcp_dport, tcphdr.tcp_seq, tcphdr.tcp_ack, tcphdr.tcp_off, tcphdr.tcp_res, tcphdr.tcp_flags, tcphdr.tcp_win, tcphdr.tcp_csum, tcphdr.tcp_urp FROM tcphdr WHERE tcphdr.cid = ".$myrow["cid"]." AND tcphdr.sid = ".$myrow["sid"]  or die("Error in query");
			$resulttcp = $db->query($querytcp);
			$myrowtcp =  $db->farray($resulttcp);
			$a[$numOfEvents]["sportTCP"] = $myrowtcp["tcp_sport"];
			$a[$numOfEvents]["dportTCP"] = $myrowtcp["tcp_dport"];
			$a[$numOfEvents]["seqTCP"] = $myrowtcp["tcp_seq"];
			$a[$numOfEvents]["ackTCP"] = $myrowtcp["tcp_ack"];
			$a[$numOfEvents]["offTCP"] = $myrowtcp["tcp_off"];
			$a[$numOfEvents]["resTCP"] = $myrowtcp["tcp_res"];
			$a[$numOfEvents]["flagsTCP"] = $myrowtcp["tcp_flags"];
			$a[$numOfEvents]["winTCP"] = $myrowtcp["tcp_win"];
			$a[$numOfEvents]["csumTCP"] = $myrowtcp["tcp_csum"];
			$a[$numOfEvents]["urpTCP"] = $myrowtcp["tcp_urp"];
			$db->free($resulttcp);
	
			// Get detail on TCP Options, if any
			$queryTCPOpt = "SELECT optid, opt_code, opt_len, opt_data FROM opt WHERE opt.cid = ".$myrow["cid"]." AND opt.sid = ".$myrow["sid"] or die("Error in query");
			$resultTCPOpt = $db->query($queryTCPOpt);
			$numOfOpts = 0;
			while($myrowTCPOpt =  $db->farray($resultTCPOpt)) {
				if ( empty($a[$numOfEvents]["numOfOpts"]) ) $a[$numOfEvents]["numOfOpts"] = 0;
				$a[$numOfEvents]["numOfOpts"]++;
				$x = ($a[$numOfEvents]["numOfOpts"]);
				$a[$numOfEvents]["codeOpt"][$x] = $myrowTCPOpt["opt_code"];
				$a[$numOfEvents]["lenOpt"][$x] = $myrowTCPOpt["opt_len"];
				$a[$numOfEvents]["dataOpt"][$x] = $myrowTCPOpt["opt_data"];
			}
			$db->free($resultTCPOpt);
			break;
		case 17:						//UDP
			$queryudp = "SELECT udphdr.udp_sport, udphdr.udp_dport, udphdr.udp_len, udphdr.udp_csum FROM udphdr WHERE udphdr.cid = ".$myrow["cid"]." AND udphdr.sid = ".$myrow["sid"] or die("Error in query");
			$resultudp = $db->query($queryudp);
			$myrowudp =  $db->farray($resultudp);
			$a[$numOfEvents]["sportUDP"] = $myrowudp["udp_sport"];
			$a[$numOfEvents]["dportUDP"] = $myrowudp["udp_dport"];
			$a[$numOfEvents]["lenUDP"] = $myrowudp["udp_len"];
			$a[$numOfEvents]["csumUDP"] = $myrowudp["udp_csum"];
			$db->free($resultudp);
			break;
	}
	$numOfEvents++;

}

// memory flush
$db->free($result);

// check for existence of IP as a destination or source

if ($type == "src") {
	$query = "SELECT event.cid, iphdr.ip_dst FROM event, iphdr WHERE event.cid = iphdr.cid AND iphdr.ip_dst = ".$ipAddress." AND event.timestamp > ".$db->timestamp($beginTime)." AND event.timestamp < ".$db->timestamp($endTime) or die("Error in query");

	// strip out the bad stuff
	$query = ereg_replace("[^A-Za-z0-9\(\)\.\_\,\ \>\<\=]", "", $query);

	$result = $db->query($query);
	$num_dst = $db->numrows($result); 
	$db->free($result);
} else {
	$query = "SELECT event.cid, iphdr.ip_src FROM event, iphdr WHERE event.cid = iphdr.cid AND iphdr.ip_src = ".$ipAddress." AND event.timestamp > ".$db->timestamp($beginTime)." AND event.timestamp < ".$db->timestamp($endTime) or die("Error in query");

	// strip out the bad stuff
	$query = ereg_replace("[^A-Za-z0-9\(\)\.\_\,\ \>\<\=]", "", $query);

	$result = $db->query($query);
	$num_src = $db->numrows($result); 
	$db->free($result);
}


//output header
$friendlyIPAddress = dec2IP ($ipAddress);
$hostname = gethostbyaddr($friendlyIPAddress);
?>
&nbsp; &nbsp; <span class="sigtitle">IP Address: <?=$friendlyIPAddress?> (<?=$hostname?>)</span><br><br>

<span class="timeblock"><b>Whois lookup:</b>
<a href="http://ws.arin.net/whois/?queryinput=<?=$friendlyIPAddress?>&B1=Submit+Query" target=extwin>ARIN</a>
<a href="http://www.ripe.net/perl/whois?query=<?=$friendlyIPAddress?>&.=Submit+Query" target=extwin>RIPE</a>
<a href="http://www.apnic.net/apnic-bin/whois.pl?search=<?=$friendlyIPAddress?>" target=extwin>APNIC</a>
<a href="http://www.dnsstuff.com/tools/whois.ch?ip=<?=$friendlyIPAddress?>&targetnic=auto" target=extwin>DNSStuff</a><br>
<b>DNS lookup:</b>
<a href="http://andrew.triumf.ca/cgi-bin/gethost?<?=$friendlyIPAddress?>" target=extwin>TRIUMF</a>
<br><br>


<!-- Can update these in the future from traceroute.org -->
<b>Traceroutes:</b>
<table border=0 cellpadding=2 cellspacing=2>
<tr>
<td class="trace"><a href="http://www.washington.edu/networking/tools/traceroute?search_address=<?=$friendlyIPAddress?>" target=extwin>USA, Washington</a></td>
<td class="trace"><a href="http://www.sdsc.edu/~hutton/cgi-bin/tracert.cgi?<?=$friendlyIPAddress?>" target=extwin>USA, California</a></td>
<td class="trace"><a href="http://www.xilo.net/tools/nph-traceroute.cgi?<?=$friendlyIPAddress?>" target=extwin>UK, London</a></td>
<td class="trace"><a href="http://vr.visualroute.it/?go=<?=$friendlyIPAddress?>" target=extwin>Italy, Florence</a></td>
<td class="trace"><a href="http://guanabara.rederio.br/cgi-bin/nph-traceroute?<?=$friendlyIPAddress?>" target=extwin>Brazil</a></td
</tr><tr>

<td class="trace"><a href="http://darkwing.uoregon.edu/~llynch/cgi-bin/uo/trace.cgi?<?=$friendlyIPAddress?>" target=extwin>USA, Oregon</a></td>
<td class="trace"><a href="http://www.net.cmu.edu/cgi-bin/netops.cgi?query=<?=$friendlyIPAddress?>&op=traceroute&.submit=&.cgifields=op" target=extwin>USA, Pennsylvania</a></td>
<td class="trace"><a href="http://www.visualroute.ffs.net/?go=<?=$friendlyIPAddress?>" target=extwin>Germany</a></td>
</tr></table>
<br>

<b>Earliest Alert from This IP:</b> <?=$earliestAlert?><br>
<b>Latest Alert from This IP:</b> <?=$latestAlert?><br>
<b>NBTscan this IP:</b><A HREF=./nbtscan.php?target=<?=$friendlyIPAddress?>> Go</a><br>
<b>Nmap this IP:</b><A HREF=./nmap.php?target=<?=$friendlyIPAddress?>> Go</a><br>
<br>

<?
if (sizeof($b[signature]) == 1) {
	echo "1 signature is present for $friendlyIPAddress as a ";
	if($type == "src"){ echo "Source"; } else { echo "Destination"; }
	echo ":<br>\n";
} else {
	echo sizeof($b[signature]) . " different signatures are present for $friendlyIPAddress as a ";
	if($type == "src"){ echo "Source"; } else { echo "Destination"; }
	echo ":<br>\n";
}
echo "<ul>";
//while ($res=each($b[signature])) {
//	echo "$res[1] Instances of &quot;$res[0]&quot;<br>\n";
//}
foreach ($b[signature] as $sig => $num) {
	echo "<li>$num Instance";
	if ($num >1) { echo "s"; }
	echo " of <a href=\"sigdetail.php?signature=" . urlencode($sig) . "&sigid={$b["sigid"][$sig]}&sigsid={$b["sigsid"][$sig]}&FQDN=$FQDN&beginTime=$beginTime&endTime=$endTime\">{$sig}</a>";
        if ($b["sigsid"][$sig] >=103) { echo " <a href=\"http://www.snort.org/search/sid/{$b["sigsid"][$sig]}\" target=extwin>[sid {$b["sigsid"][$sig]}]</a>"; }
	echo "<br>\n";
}
echo "</ul>\n";
echo "<b>Total number of events:</b> $numOfEvents<br><br>\n";

if ($type == "src") {
	if ($num_dst == 0) {
		echo "No signatures with $friendlyIPAddress as a destination";
	} else {
		echo "<a href=\"ipdetail.php?type=dst";
		echo "&ipAddress=$ipAddress&beginTime=$beginTime&endTime=$endTime\">Signatures with $friendlyIPAddress as a Destination ($num_dst event";
		if ($num_dst >1) { echo "s"; }
		echo ")</a>";
	}
} else {
	if ($num_src == 0) {
		echo "No signatures with $friendlyIPAddress as a source";
	} else {
		echo "<a href=\"ipdetail.php?type=src";
		echo "&ipAddress=$ipAddress&beginTime=$beginTime&endTime=$endTime\">Signatures with $friendlyIPAddress as a Source ($num_src event";
		if ($num_src >1) { echo "s"; }
		echo ")</a>";
	}
}
?>
</span><br><br>
<!-- Set up signatures table -->
<table width="800" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="800" valign="top">
<table border="1" width="800" cellspacing="0" bordercolor="#000000">
<tr>
<td width="800" align="center" class="detailsigs" colspan="2">Signatures with <?=$friendlyIPAddress?> as a <? if($type == "src"){ echo "Source"; } else { echo "Destination"; }?> (<?=$numOfEvents?> event<? if($numOfEvents > 1){ echo "s"; }?>)</td>
</tr>
<?
	// print all records in array
	$i = 0;
    while ($i < $numOfEvents) {
		//$line1 = "CID:{$a[$i]["cid"]} [**] <a href=\"http://www.snort.org/snort-db/sid.html?id={$a[$i][sig_sid]}\" target=extwin>{$a[$i]["signature"]}</a> [**]";
		$line1 = "CID:{$a[$i]["cid"]} [**] <a href=\"sigdetail.php?signature=" . urlencode($a[$i]["signature"]) . "&sigid={$a[$i][sig_id]}&sigsid={$a[$i][sig_sid]}&FQDN=$FQDN&beginTime=$beginTime&endTime=$endTime\">{$a[$i]["signature"]}</a> [**]";
		$line2 = "{$a[$i]["timestamp"]}";
		$line3 = "TTL:{$a[$i]["ttlIP"]} TOS:0x".dec2hex($a[$i]["tosIP"])." ID:{$a[$i]["idIP"]} IPLen:{$a[$i]["lenIP"]}  HLen:{$a[$i]["hlenIP"]} CSumIP:0x".dec2hex($a[$i]["csumIP"]);
//		echo "flagsIP: {$a[$i]["flagsIP"]}<br>";
//		echo "offIP: {$a[$i]["offIP"]}<br>";
		$srcAddress = dec2IP ($a[$i]["srcIP"]);
		$dstAddress = dec2IP ($a[$i]["dstIP"]);
		$line6 = "";
		$line7 = "";
	switch ($a[$i]["protoIP"]) {
		case 1:							//ICMP
//			echo "csumICMP: {$a[$i]["csumICMP"]}<br>";
			$line2 = $line2." <a href=\"ipdetail.php?type=src&FQDN=$FQDN&ipAddress=" . IP2dec($srcAddress) . "&beginTime=$beginTime&endTime=$endTime\">$srcAddress</a> -> <a href=\"ipdetail.php?type=dst&FQDN=$FQDN&ipAddress=" . IP2dec($dstAddress) . "&beginTime=$beginTime&endTime=$endTime\">$dstAddress</a>";
			$line3 = "ICMP ".$line3;
			$line4 = "Type:{$a[$i]["typeICMP"]} Code:{$a[$i]["codeICMP"]} ID:{$a[$i]["idICMP"]} Seq:{$a[$i]["seqICMP"]}";
			$line5 = "";
			break;
		case 6:							//TCP
			// $line2 = $line2." $srcAddress:<a href=\"http://snort.sourcefire.com/ports.html?port={$a[$i]["sportTCP"]}\" target=extwin>{$a[$i]["sportTCP"]}</a> -> $dstAddress:<a href=\"http://snort.sourcefire.com/ports.html?port={$a[$i]["dportTCP"]}\" target=extwin>{$a[$i]["dportTCP"]}</a>";
			$line2 = $line2." <a href=\"ipdetail.php?type=src&FQDN=$FQDN&ipAddress=" . IP2dec($srcAddress) . "&beginTime=$beginTime&endTime=$endTime\">$srcAddress</a>:<a href=\"http://logs.sofaware.com/resolveport/?portnumber={$a[$i]["sportTCP"]}&protocol=TCP\" target=extwin>{$a[$i]["sportTCP"]}</a> -> <a href=\"ipdetail.php?type=dst&FQDN=$FQDN&ipAddress=" . IP2dec($dstAddress) . "&beginTime=$beginTime&endTime=$endTime\">$dstAddress</a>:<a href=\"http://logs.sofaware.com/resolveport/?portnumber={$a[$i]["dportTCP"]}&protocol=TCP\" target=extwin>{$a[$i]["dportTCP"]}</a>";
			$line3 = "TCP ".$line3;
			$line4 = convertTCPFlags($a[$i]["flagsTCP"])." Seq:0x".dec2hex($a[$i]["seqTCP"])." Ack:0x".dec2hex($a[$i]["ackTCP"])." Win:0x".dec2hex($a[$i]["winTCP"])." CSumTCP:0x".dec2hex($a[$i]["csumTCP"]);
			//Assemble TCP Options
			if (!empty($a[$i]["numOfOpts"])) {
				$line5 = "TCP Options (".$a[$i]["numOfOpts"].") =>";
				$j = 1;
				while ($j <= $a[$i]["numOfOpts"]) {
					switch ($a[$i]["codeOpt"][$j]) {
							case 1:							//No Operation
								$line5 = $line5." NO-OP";
								break;
							case 2:							//Max Segment Size
								$line5 = $line5." MSS:".$a[$i]["dataOpt"][$j];
								break;
							case 3:							//Window Scale Factor
								$line5 = $line5." WS:".$a[$i]["dataOpt"][$j];
								break;
							case 4:							//SACK Permitted
								$line5 = $line5." SACKOK";
								break;
							case 5:							//SACK
								$line5 = $line5." SACK";
								break;
							case 6:							//Echo
								$line5 = $line5." ECHO";
								break;
							case 7:							//Echo Reply
								$line5 = $line5." ECHO-REPLY";
								break;
							case 8:							//Timestamp
								$line5 = $line5." Timestamp:".substr($a[$i]["dataOpt"][$j], 0, 8);
								break;
					}
					$j++;
				}
			} else {
				$line5 = "";
			}
//			echo "offTCP: {$a[$i]["offTCP"]}<br>";
//			echo "resTCP:{$a[$i]["resTCP"]}<br>";
			break;
		case 17:						//UDP
			// $line2 = $line2." $srcAddress:<a href=\"http://snort.sourcefire.com/ports.html?port={$a[$i]["sportUDP"]}\" target=extwin>{$a[$i]["sportUDP"]}</a> -> $dstAddress: <a href=\"http://snort.sourcefire.com/ports.html?port={$a[$i]["dportUDP"]}\" target=extwin>{$a[$i]["dportUDP"]}</a>";
			$line2 = $line2." <a href=\"ipdetail.php?type=src&FQDN=$FQDN&ipAddress=" . IP2dec($srcAddress) . "&beginTime=$beginTime&endTime=$endTime\">$srcAddress</a>:<a href=\"http://www.portsdb.org/bin/portsdb.cgi?portnumber={$a[$i]["sportUDP"]}&protocol=UDP\" target=extwin>{$a[$i]["sportUDP"]}</a> -> <a href=\"ipdetail.php?type=dst&FQDN=$FQDN&ipAddress=" . IP2dec($dstAddress) . "&beginTime=$beginTime&endTime=$endTime\">$dstAddress</a>:<a href=\"http://www.portsdb.org/bin/portsdb.cgi?portnumber={$a[$i]["dportUDP"]}&protocol=UDP\" target=extwin>{$a[$i]["dportUDP"]}</a>";
			$line3 = "UDP ".$line3;
			$line4 = "Len:0x".dec2hex($a[$i]["lenUDP"])." CSum:0x".dec2hex($a[$i]["csumUDP"]);
			break;
	}
	if ($a[$i]["payload"] != "" AND $a[$i]["protoIP"] != 1) {
		$asciiPayload = hex2str ($a[$i]["payload"]);
		$hexPayload = $a[$i]["payload"];

        $length = strlen($hexPayload);
        $j = 0;
		$hex = "";
        for ($j = 0; $j < $length; $j=$j+4 ) {
	        $hex=$hex.$hexPayload[$j].$hexPayload[$j+1].$hexPayload[$j+2].$hexPayload[$j+3]." ";
		}
		$hexPayload = $hex;

		$length = strlen($hexPayload);
		$j = 0;
		$hex = "";
	    for ($j = 0; $j < $length/50 ; $j=$j+1 ) {
			$tmp = substr($hexPayload, ($j * 50), 50);
	        $hex=$hex.$tmp."<br>";
		}

        $length = strlen($asciiPayload);
        $j = 0;
		$ascii = "";
        for ($j = 0; $j < $length ; $j=$j+20 ) {
			$tmp = substr($asciiPayload, $j, 20);
	        $ascii=$ascii.$tmp."<br>";
		}

		$line6 = "Payload (Hex):<br>$hex";
		$line7 = "Payload (ASCII):<br>$ascii";
	}

	// output signatures
	echo "<tr><td width=\"800\" colspan=\"2\" class=\"detaildumphead\">";
	echo "$line1<br>";
	echo "$line2<br>";
	echo "$line3<br>";
	echo "$line4<br>";
	if ($line5 != "") echo "$line5<br>";
	echo "</td></tr>";
	if ($line6 != "") {
		echo "<tr><td width=\"420\" valign=\"top\" class=\"detailpayload\">";
		echo "$line6</td>";
	}
	if ($line7 != "") {
		echo "<td width=\"180\" valign=\"top\" class=\"detailpayload\">";
		echo "$line7</td></tr>";
	}
	echo "\n";
    $i++;
    }
echo "</table>";
echo "</table>";

require_once("page_bottom.php");
?>
