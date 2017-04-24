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
session_start();
$title = "SNORT Report";
require_once("page_top.php");
require_once("timespan.php");

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

$beginTime = $_GET['beginTime'];
$endTime = $_GET['endTime'];

if (!isset($beginTime)) { $beginTime = time()-86400; }
if (!isset($endTime))   { $endTime   = -1; }
if ($endTime == "24H")  { $endTime   = $beginTime+86400; }

$timespan = new timespan($beginTime, $endTime);

$EventTimeConstraint = $timespan->getSQL($db, 'event.timestamp');

// Find earliest and latest alerts:
$query = "SELECT " . $db->timeSinceEpoch('MIN(timestamp)') . " as earliest, " . 
		$db->timeSinceEpoch('MAX(timestamp)') . " as latest FROM event " .
		"WHERE " . $EventTimeConstraint or die("Error in query");

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__ . " - before running min/max timestamp event query");

// run the query on the database
$result = $db->query($query);

$res = $db->farray($result);
$earliestAlert = @$res["earliest"];
$latestAlert = @$res["latest"];
unset($res);
$db->free($result);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__ . " - after processing min/max timestamp event query");

// set up the SQL query
$query = "SELECT event.cid, event.sid, event.signature, ".$db->timeSinceEpoch('event.timestamp').
         " AS timestamp, iphdr.cid, iphdr.ip_src, iphdr.ip_dst, signature.sig_name, signature.sig_id, signature.sig_sid, signature.sig_priority ".
         "FROM event INNER JOIN iphdr ON event.cid = iphdr.cid AND event.sid = iphdr.sid ".
         "INNER JOIN signature ON event.signature = signature.sig_id ".
 		 "WHERE $EventTimeConstraint AND signature.sig_name NOT LIKE ".$db->quote('spp_portscan%') 
 		 or die("Error in query");

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__ . " - before running non-portscan event query");

// run the query on the database
$result = $db->query($query);

// No point in calculating this when we can get it straight from the result
$alert_count = $db->numrows($result);			

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__ . " - after running non-portscan event query ($alert_count alerts returned)");

// iterate through all the records

while ($myrow =  $db->farray($result)) {   
	$sig_id = $myrow["sig_id"];
	$sig_sid = $myrow["sig_sid"];
	$sig_priority = $myrow["sig_priority"];
	$current_sig = &$a[$sig_id];

	if (empty($current_sig)) {
		$current_sig["numalerts"] = 1;
		$current_sig["signature"] = $myrow["sig_name"];
		$current_sig["sig_id"] = $myrow["sig_id"];
		$current_sig["sig_sid"] = $myrow["sig_sid"];
		$current_sig["sig_priority"] = $myrow["sig_priority"];
		$current_sig["srcIPs"] = array($myrow["ip_src"] => 1);
		$current_sig["dstIPs"] = array($myrow["ip_dst"] => 1);
	} else {
		$current_sig["numalerts"]++;

		// Increment the counters for this alert's source and destination IPs
	
		$ip_src = $myrow["ip_src"];
		$ip_dst = $myrow["ip_dst"];
	
		if (empty($current_sig["srcIPs"][$ip_src])) {
			$current_sig["srcIPs"][$ip_src] = 1;
		} else {
			$current_sig["srcIPs"][$ip_src]++;
		}

		if (empty($current_sig["dstIPs"][$ip_dst])) {
			$current_sig["dstIPs"][$ip_dst] = 1;
		} else {
			$current_sig["dstIPs"][$ip_dst]++;
		}
	}
}
$db->free($result);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

// run query for portscans
$query = "SELECT event.cid, event.signature, ".$db->timeSinceEpoch('event.timestamp').
	 " AS timestamp, signature.sig_name ".
	 "FROM event, signature WHERE $EventTimeConstraint AND event.signature = signature.sig_id " .
	 " AND signature.sig_name LIKE ".$db->quote("spp_portscan%") or die("Error in query");

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__ . " - before portscan event query");

// run the query on the database
$result = $db->query($query);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__ . " - after portscan event query");

$a["portscans"]["signature"] = "Portscans";
$a["portscans"]["numalerts"] = $db->numrows($result);
$a["portscans"]["sig_id"] = -1;
$a["portscans"]["srcIPs"] = array();
$a["portscans"]["dstIPs"] = array();
$portscan_total=$a[2]['numalerts']+$a[3]['numalerts']+$a[12]['numalerts'];
$portscan_sig = &$a["portscans"];

// iterate through all the records
while($myrow =  $db->farray($result)) {
	if (strstr("spp_portscan", $myrow["sig_name"])) {
		if ( preg_match( "([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})", $myrow["sig_name"], $regs ) ) {
		   $ip_src = "$regs[1].$regs[2].$regs[3].$regs[4]";
		}

		// Increment the counters for this alert's source and destination IPs
	
		if (empty($current_sig["srcIPs"][$src_ip])) {
			$current_sig["srcIPs"][$ip_src] = 1;
		} else {
			$current_sig["srcIPs"][$ip_src]++;
		}

		continue;
	}
}
$db->free($result);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

$alert_count = 0;
foreach ($a as $alert) {
	$alert_count += $alert["numalerts"];	
}

// sort signatures by number of alerts

function CustomAlertSort($k1, $k2) {
	global $a;
	
	$n1 = $a[$k1]["sig_priority"];
	$n2 = $a[$k2]["sig_priority"];

	if (empty($n1) or $n1 <=0) { $n1 = 9999; }
	if (empty($n2) or $n2 <=0) { $n2 = 9999; }

	if ($n1 == $n2) {
		$n1b = $a[$k1]["numalerts"];
		$n2b = $a[$k2]["numalerts"];
		if ($n1b == $n2b) return 0;
		return ($n1b > $n2b) ? -1 : 1;
	}
	return ($n1 > $n2) ? 1 : -1;
}

uksort($a, "CustomAlertSort");

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

// We will now retrieve the URLs for any references for each signature
// When we are finished, $SigReferences will be an array indexed by
// signature ID where each element is an array containing the full text
// for all references for that ID

$SigIDs = array();
$SigReferences = array();
foreach ($a as $Alert) {
	if (!empty( $Alert["sig_id"])) {
		$SigIDs[] = $Alert["sig_id"];
	}
}

$SigIDs = array_unique($SigIDs);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

if (count($SigIDs)) {
	$SigRefs = $db->query("select sig_id, ref_system_name, ref_tag from sig_reference inner join reference on sig_reference.ref_id = reference.ref_id inner join reference_system on reference.ref_system_id=reference_system.ref_system_id where sig_id in (" . implode(',', $SigIDs) . ")");
	
	if ($db->numrows($SigRefs) > 0) {
		while (list($SigID, $RefSystem, $RefTag) = $db->farray($SigRefs)) {
			$url = reference_url($RefSystem, $RefTag);
			if (!empty($url)) {
				$SigReferences[$SigID][] = "<a href=\"$url\" target=extwin>[$RefSystem $RefTag]</a>";
			} else {
				$SigReferences[$SigID][] = "[$RefSystem $RefTag]";
			}
		}
	}
}

unset($SigIDs);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

// Set up variables for counting of TCP, UDP, and ICMP alerts for pie chart
$numOfTCPAlerts = 0;
$numOfUDPAlerts = 0;
$numOfICMPAlerts = 0;

// Count TCP alerts
$query = "SELECT count(*) FROM event INNER JOIN tcphdr ON event.cid = tcphdr.cid WHERE " . $EventTimeConstraint;

$result = $db->query($query);
$numOfTCPAlerts = $db->result($result, 0);
$db->free($result);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

// Count UDP alerts
$query = "SELECT count(*) FROM event INNER JOIN udphdr ON event.cid = udphdr.cid WHERE " . $EventTimeConstraint;
$result = $db->query($query);
$numOfUDPAlerts = $db->result($result, 0);
$db->free($result);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

// Count ICMP alerts
$query = "SELECT count(*) FROM event INNER JOIN icmphdr ON event.cid = icmphdr.cid WHERE " . $EventTimeConstraint;
$result = $db->query($query);
$numOfICMPAlerts = $db->result($result, 0);
$db->free($result);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

?>

<!-- output header -->
<table width="800" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="800" valign="top" align="center">
<!-- <span class="head">SNORT REPORT</span> -->
<img src="srlogo.png" alt="SnortReport Logo" width="298" height="100">
</td>
</tr></table>

<!-- output time data and histogram -->
<div class="histogram">
<?
$timestamp_ary = array();
$query = 'SELECT `timestamp` FROM event WHERE '.$EventTimeConstraint;
$result = $db->query($query);
while($myrow =  $db->farray($result)) {
	$timestamp_ary[] = strtotime($myrow[0]);
}

function round_to_nearest_10min($timestamp){
	if(date("i", ($timestamp+60*10)) < 10){
		return mktime(
				date("H", ($timestamp+60*10)),
				0,
				0,
				date("m", ($timestamp+60*10)),
				date("d", ($timestamp+60*10)),
				date("Y", ($timestamp+60*10))
			);
	}else if(date("i", ($timestamp+60*10)) < 20){
		return mktime(
				date("H", ($timestamp+60*10)),
				10,
				0,
				date("m", ($timestamp+60*10)),
				date("d", ($timestamp+60*10)),
				date("Y", ($timestamp+60*10))
			);
	}else if(date("i", ($timestamp+60*10)) < 30){
		return mktime(
				date("H", ($timestamp+60*10)),
				20,
				0,
				date("m", ($timestamp+60*10)),
				date("d", ($timestamp+60*10)),
				date("Y", ($timestamp+60*10))
			);
	}else if(date("i", ($timestamp+60*10)) < 40){
		return mktime(
				date("H", ($timestamp+60*10)),
				30,
				0,
				date("m", ($timestamp+60*10)),
				date("d", ($timestamp+60*10)),
				date("Y", ($timestamp+60*10))
			);
	}else if(date("i", ($timestamp+60*10)) < 50){
		return mktime(
				date("H", ($timestamp+60*10)),
				40,
				0,
				date("m", ($timestamp+60*10)),
				date("d", ($timestamp+60*10)),
				date("Y", ($timestamp+60*10))
			);
	}else{
		return mktime(
				date("H", ($timestamp+60*10)),
				50,
				0,
				date("m", ($timestamp+60*10)),
				date("d", ($timestamp+60*10)),
				date("Y", ($timestamp+60*10))
			);
	}
}

function round_to_nearest_qhour($timestamp){
	if(date("i", ($timestamp+60*15)) < 15){
		return mktime(
				date("H", ($timestamp+60*15)),
				0,
				0,
				date("m", ($timestamp+60*15)),
				date("d", ($timestamp+60*15)),
				date("Y", ($timestamp+60*15))
			);
	}else if(date("i", ($timestamp+60*15)) < 30){
		return mktime(
				date("H", ($timestamp+60*15)),
				15,
				0,
				date("m", ($timestamp+60*15)),
				date("d", ($timestamp+60*15)),
				date("Y", ($timestamp+60*15))
			);
	}else if(date("i", ($timestamp+60*15)) < 45){
		return mktime(
				date("H", ($timestamp+60*15)),
				30,
				0,
				date("m", ($timestamp+60*15)),
				date("d", ($timestamp+60*15)),
				date("Y", ($timestamp+60*15))
			);
	}else{
		return mktime(
				date("H", ($timestamp+60*15)),
				45,
				0,
				date("m", ($timestamp+60*15)),
				date("d", ($timestamp+60*15)),
				date("Y", ($timestamp+60*15))
			);
	}
}

function round_to_nearest_halfhour($timestamp, $current_time=false){
	if(date("i", ($timestamp+60*30)) < 30){
		return mktime(
				date("H", ($timestamp+60*30)),
				0,
				0,
				date("m", ($timestamp+60*30)),
				date("d", ($timestamp+60*30)),
				date("Y", ($timestamp+60*30))
			);
	}else{
		return mktime(
				date("H", ($timestamp+60*30)),
				30,
				0,
				date("m", ($timestamp+60*30)),
				date("d", ($timestamp+60*30)),
				date("Y", ($timestamp+60*30))
			);
	}
}

function round_to_nearest_hour($timestamp, $current_time=false){
	return mktime(
			date("H", ($timestamp+60*60)),
			0,
			0,
			date("m", ($timestamp+60*60)),
			date("d", ($timestamp+60*60)),
			date("Y", ($timestamp+60*60))
		);
}

function round_to_nearest_day($timestamp){
	return mktime(
			0,
			0,
			0,
			date("m", ($timestamp+60*60*24)),
			date("d", ($timestamp+60*60*24)),
			date("Y", ($timestamp+60*60*24))
		);
}


if (!$haveGD or (count($timestamp_ary) == 0)) {
	echo "<IMG SRC=\"nodata.png\" HEIGHT=200 WIDTH=350>";
} else {
	require_once('GlowStick_Histogram.php');
	$gsh = new GlowStick_Histogram();
	$first_hour=0;
	$last_hour=0;
	$difft = strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd()-$timespan->getStart()));
	if($_GET['beginTime'] === '1H' || $difft < 60*60*(2)){
		//1 hour
		$last_10min = round_to_nearest_10min(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd())));
		$first_10min = round_to_nearest_10min(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd() - 60*60*1)));
//echo var_export(date("y-m-d H:i:s", $last_10min), true).'<br />';
//echo var_export(date("y-m-d H:i:s", $timespan->getEnd()), true).'<br />';
//echo var_export(date("y-m-d H:i:s", $first_10min), true).'<br />';
		$divisions = 6;
		$gsh->setDataAry($timestamp_ary, intval($divisions), $first_10min, $last_10min);
	}else if($_GET['beginTime'] === '3H' || $difft < 60*60*(4.5)){
		//3 hour
		$last_qhour = round_to_nearest_qhour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd())));
		$first_qhour = round_to_nearest_qhour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd() - 60*60*3)));
		$divisions = 12;
		$gsh->setDataAry($timestamp_ary, intval($divisions), $first_qhour, $last_qhour);
	}else if($_GET['beginTime'] === '6H' || $difft < 60*60*(9)){
		//6 hour
		$last_halfhour = round_to_nearest_halfhour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd())));
		$first_halfhour = round_to_nearest_halfhour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd() - 60*60*(6))));
		$divisions = (12);
		$gsh->setDataAry($timestamp_ary, intval($divisions), $first_halfhour, $last_halfhour);
	}else if($_GET['beginTime'] === '12H' || $difft < 60*60*(18)){
		//12 hour
		$last_hour = round_to_nearest_hour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd())));
		$first_hour = round_to_nearest_hour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd() - 60*60*12)));
		$divisions = 12;
		$gsh->setDataAry($timestamp_ary, intval($divisions), $first_hour, $last_hour);
	}else if($_GET['beginTime'] === '24H' || $difft < 60*60*(36)){
		//24 hour
		$last_hour = round_to_nearest_hour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd())));
		$first_hour = round_to_nearest_hour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd() - 60*60*24)));
		$divisions = 24;
		$gsh->setDataAry($timestamp_ary, intval($divisions), $first_hour, $last_hour);
	}else if($_GET['beginTime'] === '48H' || $difft < 60*60*(60)){
		//48 hour
		$last_hour = round_to_nearest_hour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd())));
		$first_hour = round_to_nearest_hour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd() - 60*60*48)));
		$divisions = 24;
		$gsh->setDataAry($timestamp_ary, intval($divisions), $first_hour, $last_hour);
	}else if($_GET['beginTime'] === '72H' || $difft < 60*60*24*5){
		//72 hour
		$last_hour = round_to_nearest_hour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd())));
		$first_hour = round_to_nearest_hour(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd() - 60*60*72)));
		$divisions = (12+1);
		$gsh->setDataAry($timestamp_ary, intval($divisions), $first_hour, $last_hour);
	}else if($_GET['beginTime'] === '7d' || $difft < 60*60*24*10){
		//7 days
		$last_day = round_to_nearest_day(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd())));
		$first_day = round_to_nearest_day(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd() - 60*60*24*(7+1))));
		$divisions = 7+1;
		$gsh->setDataAry($timestamp_ary, intval($divisions), $first_day, $last_day);
	}else if($_GET['beginTime'] === '14d' || $difft < 60*60*24*21){
		//14 days
		$last_day = round_to_nearest_day(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd())));
		$first_day = round_to_nearest_day(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd() - 60*60*24*(14+1))));
		$divisions = 14+1;
		$gsh->setDataAry($timestamp_ary, intval($divisions), $first_day, $last_day);
	}else if($_GET['beginTime'] === '30d' || $difft < 60*60*24*32){
		//30 days
		$last_day = round_to_nearest_day(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd())));
		$first_day = round_to_nearest_day(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd() - 60*60*24*31)));
		$divisions = 15+1;
		$gsh->setDataAry($timestamp_ary, intval($divisions), $first_day, $last_day);
	}else{
		//all
		$last_day = round_to_nearest_day(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getEnd())));
		$first_day = round_to_nearest_day(strtotime(date(FULL_DATETIME_FORMAT, $timespan->getStart()-60*60*24)));
		$divisions = 24;
		$gsh->setDataAry($timestamp_ary, intval($divisions), $first_day, $last_day);
	}

	$_SESSION['png'] = $gsh->toPNG(600, 800);
	echo '<a href="png.php"><img src="png.php"></a>';
}
?>
</div>
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		</td>
	</tr>
	<tr>
		<td width="450" height="200" valign="top" class="timeblock">
			<b>Timeframe:</b> <?= date(FULL_DATETIME_FORMAT, $timespan->getStart())?> <b>to</b> <?= date(FULL_DATETIME_FORMAT, $timespan->getEnd())?> <br>

			<?
				$curTime = date(FULL_DATETIME_FORMAT);
				echo "<b>Current Time:</b> $curTime<br>";
				echo "<b>Unique Signatures:</b> " . count($a) . "<br>";
				echo "<b>Number of Alerts:</b> $alert_count<br><br>";
				echo "<b>Earliest Alert:</b> " . date(FULL_DATETIME_FORMAT, $earliestAlert) . "<br>";
				echo "<b>Latest Alert:</b> " . date(FULL_DATETIME_FORMAT, $latestAlert) . "<br>";
			?>

			<br>
			<form action="<?=$PHP_SELF?>" METHOD="GET">
				<select name="beginTime" onChange="submit()">
					<option>Timeframe:</option>
					<option value="1H">Last 1 hour</option>
					<option value="3H">Last 3 hours</option>
					<option value="6H">Last 6 hours</option>
					<option value="12H">Last 12 hours</option>
					<option value="24H">Last 24 hours</option>
					<option value="48H">Last 48 hours</option>
					<option value="72H">Last 72 hours</option>
					<option value="7d">Last 7 days</option>
					<option value="14d">Last 14 days</option>
					<option value="30d">Last 30 days</option>
					<option value="0">All</option>
				</select>
				<input type="hidden" name="endTime" value="-1">
				<input type="image" src="go.png" align="absmiddle" alt="Go">
			</form>

			<form action="<?=$PHP_SELF?>" METHOD="GET">
				<select name="beginTime" onChange="submit()">
					<option>Day:</option>
					<?
						$edate = mktime(0,0,0,date("n,j,Y",time()-($i*86400))); 
						for ($i = 0; $i < 31; $i++) {
							$date = date("Y-m-d",$edate); 
							echo "<option value=\"$edate\">$date</option>";
							$edate-=86400;
						}
					?>
				</select>
				<input type="hidden" name="endTime" value="24H">
				<input type="image" src="go.png" align="absmiddle" alt="Go">
			</form>

		</td>
	</tr>
</table>

<!-- set up signatures table -->
<table width="800" border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign="top">
<table border="1" cellspacing="0" bordercolor="#000000">
<tr>
<td align="center" class="detailsigs" colspan="7">Detail by Signatures</td>
</tr>
<tr>
<td width=10 class="sigtitles">Num</td>
<td width=10 class="sigtitles">Prio</td>
<td width=500 class="sigtitles">Signature</td>
<td width=70 class="sigtitles"># Alerts</td>
<td width=70 class="sigtitles"># Sources</td>
<td width=70 class="sigtitles"># Dest.</td>
<td width=70 class="sigtitles">Detail</td>
</tr>

<?

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

	$i = 0;
	$class=sigblocks;
	foreach ($a as $Alert) {
		if ($Alert["numalerts"] == 0) {
			continue;
		}
		$i++;
		
		if (!empty($prevprio) and $Alert[sig_priority] != $prevprio) {
			if ($class == sigblocks) {
				$class=sigblocks2;
			} else {
				$class=sigblocks;
			}
		}
	    echo "<tr>";
		echo "<td class=\"$class\">$i</td>";
		echo "<td class=\"$class\">$Alert[sig_priority]&nbsp;</td>";
		// echo "<td class=\"sigblocks\">{$Alert["signature"]} ($Alert[sig_sid])";
		// echo "<td class=\"sigblocks\">{$Alert["signature"]} <a href=\"http://www.snort.org/snort-db/sid.html?id=$Alert[sig_sid]\">[sid $Alert[sig_sid]]</a>";
		echo "<td class=\"$class\">{$Alert["signature"]}";
		if ($Alert[sig_sid] >= 103) {
			echo " <a href=\"http://www.snort.org/search/sid/$Alert[sig_sid]\"target=extwin>[sid $Alert[sig_sid]]</a>";
		}
		if (!empty($Alert["sig_id"]) and !empty($SigReferences[$Alert["sig_id"]])) {
			echo " " . implode(" ", $SigReferences[$Alert["sig_id"]]);
		}
		echo "</td>";
		echo "<td class=\"$class\">{$Alert["numalerts"]}</td>";
		echo "<td class=\"$class\">" . count($Alert["srcIPs"]) . "</td>";
		echo "<td class=\"$class\">" . count($Alert["dstIPs"]) . "</td>";
		if ($Alert["signature"] == "Portscans") {
			$detailURL = "portscan.php?signature=" . urlencode($Alert["signature"]);
			if (!empty($Alert["sig_id"])) {
				 "&sigid=". urlencode($Alert["sig_id"]);
			}
		} else {
			$detailURL = "sigdetail.php?signature=" . urlencode( $Alert["signature"]) . "&sigid=". urlencode($Alert["sig_id"]) . "&sigsid=". urlencode($Alert["sig_sid"]);
		}
		$detailURL = $detailURL . "&FQDN=yes&GeoIP=yes&".$timespan->getURL('beginTime', 'endTime');
		echo "<td class=\"$class\"><a href=$detailURL>Summary</a></td>";
		echo "</tr>";
		$prevprio=$Alert[sig_priority];
    }
	echo "</table>";
echo "</td>";
echo "</tr>";
echo "</table>";

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

require_once("page_bottom.php");
?>
