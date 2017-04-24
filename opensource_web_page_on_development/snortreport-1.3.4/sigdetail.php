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

//add data conversion functions
require_once("functions.php");

$title = sprintf("SNORT Report - Signature Detail (%s)", htmlspecialchars($signature));
require_once("page_top.php");

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__ . " - before retrieving events");

$signature = $_GET['signature'];
$sigid = $_GET['sigid'];
$FQDN = $_GET['FQDN'];
$beginTime = $_GET['beginTime'];
$endTime = $_GET['endTime'];

$beginTime = intval($beginTime); // A value or zero
$endTime = intval($endTime) or 0xFFFFFFFF; // A value or The End of Time

$signature = ereg_replace("[^A-Za-z0-9\+\ \-\.]", "", $signature);
$sigid = ereg_replace("[^0-9]", "", $sigid);
$sigsid = ereg_replace("[^0-9]", "", $sigsid);
$FQDN = ereg_replace("[^A-Za-z\.]", "", $FQDN);
$beginTime = ereg_replace("[^0-9]", "", $beginTime);
$endTime = ereg_replace("[^0-9]", "", $endTime);
//if ($FQDN == "") {$FQDN=" ";}



assert($beginTime < $endTime);

$URLTimeConstraint = "beginTime=$beginTime&endTime=$endTime";
$DBTimeConstraint = "(event.timestamp > " . $db->timestamp($beginTime) . " AND event.timestamp < " . $db->timestamp($endTime) . ")";

$sigid = intval($sigid) or die("Invalid signature ID");

// set up the SQL query
$query = "SELECT event.cid, event.sid, iphdr.ip_src, iphdr.ip_dst, " . $db->timeSinceEpoch("event.timestamp") . " AS timestamp FROM event, iphdr WHERE event.cid = iphdr.cid AND event.sid = iphdr.sid AND event.signature = '".$sigid."' AND $DBTimeConstraint" or die("Error in query");

// run the query on the database
$result = $db->query($query);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__ . " - after retrieving events");

$earliestAlert = 0xFFFFFFFF;
$latestAlert = 0;

// Keyed by IP
$Sources = array();
$Destinations = array();

// Used to accelerate queries to determine dest counts
$UniqueCIDs = array();

while($myrow =  $db->farray($result)) {
	$ip_src = $myrow["ip_src"];
	$ip_dst = $myrow["ip_dst"];
	$timestamp = $myrow["timestamp"];
	
	$UniqueCIDs[$myrow["cid"]] = 1;
	
	if ($timestamp < $earliestAlert) $earliestAlert = $timestamp;
	if ($timestamp > $latestAlert) $latestAlert = $timestamp;

	if (empty($Sources[$ip_src])) {
		$Sources[$ip_src]['SigAlerts'] = 1;
		$Sources[$ip_src]['SigDests'] = array($ip_dst => 1);
		// These will be found later:
		$Sources[$ip_src]['TotalAlerts'] = 0;
		$Sources[$ip_src]['TotalDests'] = 0;
	} else {
		$Sources[$ip_src]['SigAlerts']++;
		if (empty($Sources[$ip_src]['SigDests'][$ip_dst])) {
			$Sources[$ip_src]['SigDests'][$ip_dst] = 1;
		} else {
			$Sources[$ip_src]['SigDests'][$ip_dst]++;
		}
	}
	
	if (empty($Destinations[$ip_dst])) {
		$Destinations[$ip_dst]["SigAlerts"] = 1;
		$Destinations[$ip_dst]["SigSources"] = array($ip_src => 1);
		// These will be found later:
		$Destinations[$ip_dst]['TotalAlerts'] = 0;
		$Destinations[$ip_dst]['TotalSources'] = 0;
	} else {
		$Destinations[$ip_dst]["SigAlerts"]++;
		if (empty($Destinations[$ip_dst]["SigSources"][$ip_src])) {
			$Destinations[$ip_dst]["SigSources"][$ip_src] = 1;
		} else {
			$Destinations[$ip_dst]["SigSources"][$ip_src]++;
		}
	}
}

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);
// Get the total number of alerts for each source

$query = "SELECT ip_src, COUNT(*) AS src_count FROM event INNER JOIN iphdr ON event.cid = iphdr.cid AND event.sid = iphdr.sid WHERE ip_src IN (" . implode(",", array_keys($Sources)) . ") AND $DBTimeConstraint GROUP BY iphdr.ip_src" or die("Error in query");
$query=str_replace ("()","(0)",$query);
$result = $db->query($query);

while ($myrow = $db->farray($result)) {
	$Sources[$myrow["ip_src"]]["TotalAlerts"] = $myrow["src_count"];
}
$db->free($result);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);
// Get the total number of destinations for each source

$query = "SELECT ip_src FROM event INNER JOIN iphdr ON event.cid = iphdr.cid AND event.sid = iphdr.sid WHERE ip_src IN (" . implode(",", array_keys($Sources)) . ") AND $DBTimeConstraint GROUP BY iphdr.ip_src, iphdr.ip_dst" or die("Error in query");
$query=str_replace ("()","(0)",$query);
$result = $db->query($query);

while ($myrow = $db->farray($result)) {
	$Sources[$myrow["ip_src"]]["TotalDests"]++;
}
$db->free($result);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);
// Get the total number of alerts for each destinations
$query = "SELECT ip_dst, COUNT(*) AS dst_count FROM event INNER JOIN iphdr ON event.cid = iphdr.cid AND event.sid = iphdr.sid WHERE ip_dst IN (" . implode(",", array_keys($Destinations)) . ") AND $DBTimeConstraint GROUP BY iphdr.ip_dst" or die("Error in query");
$query=str_replace ("()","(0)",$query);
$result = $db->query($query);

while ($myrow = $db->farray($result)) {
	$Destinations[$myrow["ip_dst"]]["TotalAlerts"] = $myrow["dst_count"];
}
$db->free($result);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);
// Get the total number of sources for each destination

$query = "SELECT ip_dst FROM event INNER JOIN iphdr ON event.cid = iphdr.cid AND event.sid = iphdr.sid WHERE ip_dst IN (" . implode(",", array_keys($Destinations)) . ") AND $DBTimeConstraint GROUP BY iphdr.ip_dst, iphdr.ip_src" or die("Error in query");
$query=str_replace ("()","(0)",$query);
$result = $db->query($query);

while ($myrow = $db->farray($result)) {
	$Destinations[$myrow["ip_dst"]]["TotalSources"]++;
}
$db->free($result);

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

echo '&nbsp; &nbsp; <span class="sigtitle">Signature: ' . htmlspecialchars($signature) . '</span>';

$SigRefs = $db->query("select ref_system_name, ref_tag from sig_reference inner join reference on sig_reference.ref_id = reference.ref_id inner join reference_system on reference.ref_system_id=reference_system.ref_system_id where sig_id=$sigid");

if (($db->numrows($SigRefs) > 0) or ($sigsid >= 103)) {
	echo "<p><span class=\"sigref\"><b>References:</b> ";

	if ($sigsid >= 103) {
		echo "<a href=\"http://www.snort.org/snort-db/sid.html?id=$sigsid\" target=extwin>[sid $sigsid]</a> ";
	}

	if ($db->numrows($SigRefs) > 0) {
		while (list($RefSystem, $RefTag) = $db->farray($SigRefs)) {
			$url = reference_url($RefSystem, $RefTag);
			if (!empty($url)) {
				echo "<a href=\"$url\" target=extwin>[$RefSystem $RefTag]</a> ";
			} else {
				echo "[$RefSystem $RefTag] ";
			}
		}
	}
	
	echo "</span></p>";
}

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

?>

<p><span class="timeblock">
<b>Earliest Such Alert:</b> <?=date(FULL_DATETIME_FORMAT, $earliestAlert)?><br>
<b>Latest Such Alert:</b> <?=date(FULL_DATETIME_FORMAT, $latestAlert)?></span></p>

<!-- set up source table -->
<table>
<tr>
<td valign="top">
<table border="1" cellspacing="0" bordercolor="#000000">
<tr>
<td align="center" class="detailsigs" colspan="<? if($FQDN == "yes"){ echo "6"; } else { echo "5";} ?>">Sources Triggering This Attack Signature</td>
</tr>
<tr>
<td class="sigtitles" width=100>Source IP</td>
<? if($FQDN == "yes") { ?>
<td class="sigtitles" width=250>FQDN</td>
<? } ?>
<td class="sigtitles" width=90># Alerts (sig)</td>
<td class="sigtitles" width=90># Alerts (total)</td>
<td class="sigtitles" width=90># Dsts (sig)</td>
<td class="sigtitles" width=90># Dsts (total)</td>
</tr>

<?
	if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

	// print all sources
	function CustomSrcSort($k1, $k2) {
		global $Sources;

		$n1 = $Sources[$k1]["SigAlerts"];
		$n2 = $Sources[$k2]["SigAlerts"];

		if ($n1 == $n2) {
			$n1b = $Sources[$k1]["TotalAlerts"];
			$n2b = $Sources[$k2]["TotalAlerts"];
			if ($n1b == $n2b) return 0;
			return ($n1b > $n2b) ? -1 : 1;
		}
		return ($n1 > $n2) ? -1 : 1;
	}

	uksort($Sources, "CustomSrcSort");

	foreach ($Sources as $IP => $Source) {
	    echo "<tr>";
		$ipAddress = dec2IP($IP);
		$ipURL = "ipdetail.php?type=src&FQDN=$FQDN&ipAddress=".$IP;

		echo "<td class=\"sigblocks\"><a href=\"$ipURL&$URLTimeConstraint\">$ipAddress</a></td>";
		if ($FQDN == "yes") echo "<td class=\"sigblocks\">" . gethostbyaddr($ipAddress) . "</td>";

		echo "<td class=\"sigblocks\">{$Source['SigAlerts']}</font></td>";
		echo "<td class=\"sigblocks\">{$Source['TotalAlerts']}</font></td>";

		echo "<td class=\"sigblocks\">" . count($Source['SigDests']) . "</font></td>";
		echo "<td class=\"sigblocks\">{$Source['TotalDests']}</font></td>";

		echo "</tr>\n";

		flush(); // Speed apparent load time (this helps a LOT with DNS lookups)
    }

	if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);
?>

</table>
</td>
</tr>
</table>
<br><br>

<!-- set up destination table -->
<table>
<tr>
<td valign="top">
<table border="1" cellspacing="0" bordercolor="#000000">
<tr>
<td align="center" class="detailsigs" colspan="<? if($FQDN == "yes"){ echo "6"; } else { echo "5";} ?>">Destinations Receiving This Attack Signature</td>
</tr>
<tr>
<td class="sigtitles" width=100>Dest IP</td>
<? if($FQDN == "yes") echo '<td class="sigtitles" width=250>FQDN</td>'; ?>
<td class="sigtitles" width=90># Alerts (sig)</td>
<td class="sigtitles" width=90># Alerts (total)</td>
<td class="sigtitles" width=90># Srcs (sig)</td>
<td class="sigtitles" width=90># Srcs (total)</td>
</tr>

<?

	if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);
	
	// Print all destinations
	function CustomDstSort($k1, $k2) {
		global $Destinations;

		$n1 = $Destinations[$k1]["SigAlerts"];
		$n2 = $Destinations[$k2]["SigAlerts"];

		if ($n1 == $n2) {
			$n1b = $Destinations[$k1]["TotalAlerts"];
			$n2b = $Destinations[$k2]["TotalAlerts"];
			if ($n1b == $n2b) return 0;
			return ($n1b > $n2b) ? -1 : 1;
		}
		return ($n1 > $n2) ? -1 : 1;
	}

	uksort($Destinations, "CustomDstSort");

	foreach ($Destinations as $IP => $Destination) {
	    echo "<tr>";
		$ipAddress = dec2IP($IP);
		$ipURL = "ipdetail.php?type=dst&FQDN=$FQDN&ipAddress=" . $IP;

		echo "<td class=\"sigblocks\"><a href=\"$ipURL&$URLTimeConstraint\">$ipAddress</a></td>";

		if ($FQDN == "yes") echo "<td class=\"sigblocks\">" . gethostbyaddr($ipAddress) . "</td>";
		echo "<td class=\"sigblocks\">{$Destination["SigAlerts"]}</td>";
		echo "<td class=\"sigblocks\">{$Destination["TotalAlerts"]}</td>";
		echo "<td class=\"sigblocks\">" . count($Destination["SigSources"]) . "</td>";
		echo "<td class=\"sigblocks\">{$Destination["TotalSources"]}</td>";
		echo "</tr>";
		flush(); // Speed apparent load time (this helps a LOT with DNS lookups)
    }
    
    if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);
?>
</table>
</td>
</tr>
</table>
<br>

<?
//Toggle between showing FQDNs
$signature = str_replace(" ","%20", $signature);			//Make URL browser-friendly
$beginTime = str_replace(" ","%20",$beginTime);
$endTime = str_replace(" ","%20",$endTime);
if ($FQDN == "yes") {
        $anchor = "Show signature without FQDNs";
        $FQDN = "no";
} else {
        $anchor = "Show signature with FQDNs";
        $FQDN = "yes";
}
$qs = "signature=$signature&sigid=$sigid&FQDN=$FQDN&beginTime=$beginTime&endTime=$endTime";
print "<b><a href=\"sigdetail.php?$qs\">$anchor</a></b><br>\n";

if (PROFILING) elapsedTimer(__FILE__ . ": " . __LINE__);

require_once("page_bottom.php");

?>
