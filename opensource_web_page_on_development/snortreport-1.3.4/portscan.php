<?php

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

// include functions
require_once('functions.php');

$title = "SNORT Report - Portscan Details";
require_once("page_top.php");

$signature = $_GET['signature'];
$sigid = $_GET['sigid'];
$FQDN = $_GET['FQDN'];
$beginTime = $_GET['beginTime'];
$endTime = $_GET['endTime'];

// Set up begin and end time of query
if ($beginTime == "") {
	$beginTime = date( FULL_DATETIME_FORMAT, mktime(date("H"),date("i"),date("s"),date("m"),date("d")-1,date("Y")) ); 
	$endTime = date(FULL_DATETIME_FORMAT);
}

// set up the SQL query
$query = "SELECT event.cid, event.signature, event.timestamp, signature.sig_name FROM event, signature WHERE signature.sig_name LIKE ".$db->quote('spp_portscan%')." AND event.signature = signature.sig_id AND event.timestamp > ".$db->timestamp($beginTime)." AND event.timestamp < ".$db->timestamp($endTime) or die("Error in query");

// run the query on the database
$result = $db->query($query);


// iterate through all the records

    $recnum = 0;								//counter for number of records
	$numOfSrcIPs = 0;
	$earliestAlert = "2010-01-01 12:00:00";		//set up earliest and latest alerts
	$latestAlert = "1990-01-01 12:00:00";

while($myrow =  $db->farray($result))
{   
    $recnum++;

    // check for earliest and latest
	if ($myrow["timestamp"] < $earliestAlert) $earliestAlert = $myrow["timestamp"];
	if ($myrow["timestamp"] > $latestAlert) $latestAlert = $myrow["timestamp"];

	//put data in source array
    $i = 0;
	$found = 0;

	if ( ereg( "([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})", $myrow["sig_name"], $regs ) ) {
	   $ret = "$regs[1].$regs[2].$regs[3].$regs[4]";
	   $ret = ip2dec($ret);
	}

	while ($i <= $numOfSrcIPs) {					//check to see if IP is already in array and increment if found
		if (!empty($s[$i]["srcIP"]) and $s[$i]["srcIP"] == $ret) {
		    $s[$i]["numAlerts"]++;
			$found = 1;
		}

		if ($found == 1) {
			break;
		}

		$i++;
	}
												//If not found, then add to array
	if ($found == 0) {
		$numOfSrcIPs++;
		$s[$numOfSrcIPs]["srcIP"] = $ret;
		$s[$numOfSrcIPs]["numAlerts"] = 1;
	}

}
$db->free($result);

?>

<!-- output portscans -->
&nbsp; &nbsp; <span class="sigtitle">Signature: <?=$signature?></span><br><br>

<span class="timeblock">
<b>Earliest Such Alert:</b> <?=$earliestAlert?><br>
<b>Latest Such Alert:</b> <?=$latestAlert?><br><br>
</span>

<!-- set up table -->
<table>
<tr>
<td valign="top">
<table border="1" cellspacing="0" bordercolor="#000000">
<tr>
<td align="center" class="detailsigs" colspan="<? if($FQDN == "yes") { echo "3"; } else { echo "2"; }?>">
Sources Triggering This Attack Signature</td>
</tr>
<tr>
<td width=100 class="sigtitles">Source IP</td>
<?
	if ($FQDN == "yes") {
		echo "<td width=250 class=\"sigtitles\">FQDN</td>";
	}
?>
<td width=90 class="sigtitles"># Alerts (sig)</td>

</tr>

<?
	$i = 1;
    while ($i <= $numOfSrcIPs) {
	    echo "<tr>";
		$ipAddress = dec2ip($s[$i]["srcIP"]);
		$ipURL = "psdetail.php?type=src&ipAddress=".$s[$i]["srcIP"];
		if ($FQDN == "yes") $hostname = gethostbyaddr($ipAddress);
		$beginTime = str_replace(" ","%20",$beginTime);
		$endTime = str_replace(" ","%20",$endTime);
		echo "<td class=\"sigblocks\"><a href=$ipURL&beginTime=$beginTime&endTime=$endTime>$ipAddress</a></td>";
		if ($FQDN == "yes") echo "<td class=\"sigblocks\">$hostname</td>";
		echo "<td class=\"sigblocks\">{$s[$i]["numAlerts"]}</td>";
		echo "</tr>";
	    $i++;
    }
?>
</table>
</td>
</tr>
</table>
<br><br>

<?

//Toggle between showing FQDNs
$signature = str_replace(" ","%20",$signature);						//Make URL browser-friendly
$beginTime = str_replace(" ","%20",$beginTime);
$endTime = str_replace(" ","%20",$endTime);
if($FQDN == "yes"){
	$anchor = "Show signature without FQDNs";
	$FQDN   = 'no';
} else {
	$anchor = "Show signature with FQDNs";
	$FQDN   = 'yes';
}
$qs = "signature=$signature&FQDN=$FQDN&beginTime=$beginTime&endTime=$endTime";
print "<b><a href=\"portscan.php?$qs\">$anchor</a></b><br>";
require_once("page_bottom.php");
?>
