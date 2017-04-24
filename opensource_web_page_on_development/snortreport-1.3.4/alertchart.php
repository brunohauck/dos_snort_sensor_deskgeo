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

// include variables
require('srconf.php'); 

// include jpgraph libraries
include (JPGRAPH_PATH . "jpgraph.php");
include (JPGRAPH_PATH . "jpgraph_pie.php");

$tcp = $_GET['tcp'];
$udp = $_GET['udp'];
$icmp = $_GET['icmp'];
$portscan = $_GET['portscan'];

$data = array($tcp,$udp,$icmp,$portscan);

// Create the pie graphSetCenter
$graph = new PieGraph(350,200,"");
$graph->SetMarginColor (array(204, 204, 153));
$graph->SetColor (array(204, 204, 153));
$graph->SetFrame (1, array(204, 204, 153));

$graph->legend->SetFillColor(array(206, 207, 156));

// Create the pie chart
$pie = new PiePlot($data);
$pie->SetCenter(0.3,0.55);
$pie->SetTheme('sand');
$pie->SetLegends(array("TCP ({$data[0]})","UDP ({$data[1]})","ICMP ({$data[2]})","Portscan ({$data[3]})"));

$graph->Add($pie);

// Manually Create Title
$text = new Text("Types of Traffic", 0, 0);
$text->Pos(0.3, 0.05);

$text->SetFont(FF_FONT1,FS_BOLD);

$graph->AddText($text);


$graph->Stroke();

?>
