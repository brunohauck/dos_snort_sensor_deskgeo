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


require_once("functions.php");
$type == "src" ? $txt = "Source" : $txt = "Destination";
$title = sprintf("SNORT Report - Portscans for %s as a %s",
					dec2IP($ipAddress), $txt);

// include variables
require_once("page_top.php");



$earliestAlert = "2010-01-01 12:00:00";		//set up earliest and latest alerts
$latestAlert = "1990-01-01 12:00:00";

// set up the SQL query
$query = "SELECT event.cid, event.signature, event.timestamp, signature.sig_name FROM event, signature WHERE signature.sig_name LIKE ".$db->quote("spp_portscan%")." AND event.signature = signature.sig_id AND event.timestamp > ".$db->timestamp($beginTime)." AND event.timestamp < ".$db->timestamp($endTime) or die("Error in query");

// run the query on the database
$result = $db->query($query);

// iterate through the result set and construct array of events
$numOfEvents = 0;

while($myrow =  $db->farray($result))
{
	$test = ereg ((dec2IP ($ipAddress)), $myrow["sig_name"]);

	if ($test) {
		$a[$numOfEvents]["cid"] = $myrow["cid"];
		$a[$numOfEvents]["signature"] = $myrow["sig_name"];
		$a[$numOfEvents]["timestamp"] = $myrow["timestamp"];

		// Check for earliest and latest alerts
		if ($myrow["timestamp"] < $earliestAlert) $earliestAlert = $myrow["timestamp"]; 
		if ($myrow["timestamp"] > $latestAlert) $latestAlert = $myrow["timestamp"];
	
		$numOfEvents++;
	}
}

// memory flush
$db->free($result);

// check for existence of IP as a destination or source

$query = "SELECT event.cid, iphdr.ip_dst FROM event, iphdr WHERE event.cid = iphdr.cid AND event.sid = iphdr.sid AND iphdr.ip_dst = ".$ipAddress." AND event.timestamp > ".$db->timestamp($beginTime)." AND event.timestamp < ".$db->timestamp($endTime) or die("Error in query");
$result = $db->query($query);
$num_dst = $db->numrows($result); 
$db->free($result);

$query = "SELECT event.cid, iphdr.ip_src FROM event, iphdr WHERE event.cid = iphdr.cid AND event.sid = iphdr.sid AND iphdr.ip_src = ".$ipAddress." AND event.timestamp > ".$db->timestamp($beginTime)." AND event.timestamp < ".$db->timestamp($endTime) or die("Error in query");
$result = $db->query($query);
$num_src = $db->numrows($result); 
$db->free($result);


//output header
$friendlyIPAddress = dec2IP ($ipAddress);
$hostname = gethostbyaddr($friendlyIPAddress); 
?>
<span class="sigtitle">IP Address: <?=$friendlyIPAddress?> (<?=$hostname?>)</span><br><br>

<span class="timeblock"><b>Whois lookup:</b>
<a href="http://www.arin.net/cgi-bin/whois.pl?queryinput=<?=$friendlyIPAddress?>&B1=Submit+Query" target=extwin>ARIN</a>
<a href="http://www.ripe.net/perl/whois?query=<?=$friendlyIPAddress?>&.=Submit+Query" target=extwin>RIPE</a>
<a href="http://www.apnic.net/apnic-bin/whois.pl?search=<?=$friendlyIPAddress?>" target=extwin>APNIC</a>
<a href="http://www.geektools.com/cgi-bin/proxy.cgi?query=<?=$friendlyIPAddress?>&targetnic=auto" target=extwin>Geektools</a><br>
<b>DNS lookup:</b>
<a href="http://andrew.triumf.ca/cgi-bin/gethost?<?=$friendlyIPAddress?>" target=extwin>TRIUMF</a>
<br><br>


<b>Traceroutes:</b>
<table border=0 cellpadding=2 cellspacing=2>
<tr>
<td class="trace"><a href="http://visualroute.backland.net/?go=<?=$friendlyIPAddress?>" target=extwin>Canada, Ontario</a></td>
<td class="trace"><a href="http://visualroute.webhits.de/?go=<?=$friendlyIPAddress?>" target=extwin>Germany, Frankfurt</a></td>
<td class="trace"><a href="http://vr.visualroute.it/?go=<?=$friendlyIPAddress?>" target=extwin>Italy, Florence</a></td>
<td class="trace"><a href="http://www.visualroute.nedcomp.nl/?go=<?=$friendlyIPAddress?>" target=extwin>Netherlands, Rotterdam</a></td>
<td class="trace"><a href="http://visualroute.bboxbbs.ch/?go=<?=$friendlyIPAddress?>" target=extwin>Switzerland, Bern</a></td>
</tr><tr>
<td class="trace"><a href="http://www.visualroute.ffs.net/?go=<?=$friendlyIPAddress?>" target=extwin>Germany</a></td>
<td class="trace"><a href="http://visualroute.cgan.com.hk/?go=<?=$friendlyIPAddress?>" target=extwin>Hong Kong</a></td>
<td class="trace"><a href="http://visualroute.ipartners.pl/?go=<?=$friendlyIPAddress?>" target=extwin>Poland</a></td>
<td class="trace"><a href="http://www.linkwan.com/visualroute/?go=<?=$friendlyIPAddress?>" target=extwin>Taiwan</a></td>
</tr></table>
<br>

<b>Earliest Alert from This IP:</b> <?=$earliestAlert?><br>
<b>Latest Alert from This IP:</b> <?=$latestAlert?><br><br>

<?

if ($num_src == 0) {
	echo "No signatures with $friendlyIPAddress as a source<br>";
} else {
	echo "<a href=\"ipdetail.php?type=src";
	echo "&ipAddress=$ipAddress&beginTime=$beginTime&endTime=$endTime\">Signatures with $friendlyIPAddress as a Source</a><br>";
}

if ($num_dst == 0) {
	echo "No signatures with $friendlyIPAddress as a destination<br>";
} else {
	echo "<a href=\"ipdetail.php?type=dst";
	echo "&ipAddress=$ipAddress&beginTime=$beginTime&endTime=$endTime\">Signatures with $friendlyIPAddress as a Destination</a><br>";
}
?>
<br>

<!-- Set up signatures table -->
<table width="600" border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign="top">
<table border="1" cellspacing="0" bordercolor="#000000">
<tr>
<td align="center" class="detailsigs" colspan="3">Portscan Details</td>
</tr>
<tr>
<td width=50 class="sigtitles">CID</td>
<td width=420 class="sigtitles">Signature</td>
<td width=130 class="sigtitles">Timestamp</td>
</tr>

<?
	foreach ($a as $Alert) {
	    echo "<tr>";
		echo "<td class=\"sigblocks\">{$Alert["cid"]}</td>";
		echo "<td class=\"sigblocks\">{$Alert["signature"]}</td>";
		echo "<td class=\"sigblocks\">{$Alert["timestamp"]}</td>";
		echo "</tr>";
    }

?>
</table>
</td>
</tr>
</table>

<?
require_once("page_bottom.php");
?>
