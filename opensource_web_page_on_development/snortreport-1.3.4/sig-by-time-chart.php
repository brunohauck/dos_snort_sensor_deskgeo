<?

// sig-by-time-chart contributed to Snort Report 1.04
// August 13, 2001
// Copyright (C) 2001 Chris Adams
//
// Generates a line chart showing the number of signatures over time
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

// include variables
require('srconf.php'); 

// include jpgraph libraries
require(JPGRAPH_PATH . "jpgraph.php");
require(JPGRAPH_PATH . "jpgraph_line.php");

$MaxSigs = 10; // Only display top $MaxSigs signatures

$WhereConstraints = array("1=1");

if (!empty($beginTime)) {
	$WhereConstraints[] = "timestamp >= ".$db->timestamp(intval($beginTime));
}

if (!empty($endTime)) {
	$WhereConstraints[] = "timestamp <= ".$db->timestamp(intval($endTime));
}

$WhereConstraint = empty($WhereConstraints) ? "" : "(" . implode(") AND (", $WhereConstraints) . ")";


$SigData = array();
// Get the ranges
$DateRanges = $db->query("SELECT ".$db->timeSinceEpoch('MIN(timestamp)').", ".$db->timeSinceEpoch('MAX(timestamp)')." FROM event INNER JOIN signature ON event.signature = signature.sig_id WHERE $WhereConstraint AND signature.sig_name NOT LIKE 'spp_portscan%'");

list($MinDate, $MaxDate) = $db->farray($DateRanges);
$MaxDate += 86400 - ($MaxDate % 86400); // Set to the end of the day

// We create all sig count arrays from this one, so they will each have the proper number of days and the lines will line up...
$NewSigArray = array();
for ($i = $MinDate; $i <= $MaxDate; $i += 86400) {
	$NewSigArray[date("Y-m-d", $i)] = .01; // Work around a "feature" in jpGraph where y=0 isn't drawn!
}

$db->free($DateRanges);

// Get the top n signatures
$TopSigs = $db->query("SELECT signature, COUNT(*) as sigcount FROM event INNER JOIN signature ON event.signature = signature.sig_id WHERE $WhereConstraint AND signature.sig_name NOT LIKE 'spp_portscan%' GROUP BY signature ORDER BY sigCount DESC LIMIT $MaxSigs");

$TopSigIDs = array();
while ($Sig = $db->farray($TopSigs)) {
	$TopSigIDs[] = $Sig[0];
}
$db->free($TopSigs);

// Get the actual sig counts
$SigsByTime = $db->query("SELECT signature, ".$db->dateFormat('timestamp', '%Y-%m-%d')." AS Date, count(*) FROM event WHERE signature IN (" . implode(",", $TopSigIDs) . ") AND $WhereConstraint GROUP BY signature, Date ORDER BY Date");

while (list($SigID, $Date, $Hits) = $db->farray($SigsByTime)) {
	if (empty($SigData[$SigID])) {
		$SigData[$SigID] = $NewSigArray;
	}
	
	if ($Hits < 1) $Hits = .01; // Work around a "feature" in jpGraph where y=0 isn't drawn!
	$SigData[$SigID][$Date] = $Hits;
}
$db->free($SigsByTime);

// Get the names for the signatures
$SigNames = array();
$SigNameQuery = $db->query("SELECT sig_id, SUBSTRING(sig_name, 1, 23) FROM signature WHERE sig_id IN(" . implode(",", $TopSigIDs) . ")");
while (list($SigID, $SigName) = $db->farray($SigNameQuery)) {
	$SigNames[$SigID] = $SigName;
}

$db->free($SigNameQuery);

function CustomSigSort($a, $b) {
	global $SigNames;
	return strcasecmp($SigNames[$a], $SigNames[$b]);
}

uksort($SigData, "CustomSigSort");

// Create the pie graph 
$graph = new Graph(1000, 600);
$graph->SetMarginColor(array(206, 207, 156));
// $graph->SetColor(array(206, 207, 156));
$graph->SetFrame(1, array(206, 207, 156));

$graph->title->Set("Top $MaxSigs Daily Alerts");
$graph->title->SetFont(FF_VERDANA, FS_BOLD, 18);

$graph->SetScale("textlin");

// TODO: Color by severity & maximize contrast
$Colors = array(
	"red",
	"green",
	"blue",
	"orange",
	"yellow",
	"brown",
	"aquamarine",
	"gray",
	"black",
	"pink"
);

$LinePlots = array();
foreach ($SigData as $SigID => $SigHits) {
	$i = count($LinePlots);
	$LinePlot = &$LinePlots[$i];

	$LinePlots[$i] = new LinePlot(array_values($SigHits));
	
	$LinePlot->SetLegend($SigNames[$SigID]);
	$LinePlot->SetColor(empty($Colors[$i]) ? "black" : $Colors[$i]); 
	$LinePlot->SetWeight(2);

	$graph->Add( $LinePlots[$i] );
}

$graph->xaxis->SetTickLabels(array_keys($NewSigArray));

$graph->img->SetMargin(40, 180, 40, 20);
$graph->legend->Pos(0.005, 0.025, "right", "top");

$graph->Stroke();

?>
