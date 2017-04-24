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

// Current version of Snort Report
$srVersion = "Snort Report Version 1.3.4";

//
// PLEASE SET THE FOLLOWING VARIABLES TO MATCH YOUR SYSTEM
//

// Put your snort database login credentials in this section
$server = "localhost";
$user = "root";
$pass = "deskgeo1234567890";
$dbname = "snort";

// use either "mysql" or "pgsql" below, depending on your database
$dbtype = "mysql";

// Change to FALSE if GD *and* JPGraph are not installed
$haveGD = TRUE;

// Relative path to JPGraph
// You need to have jpgraph and jpgraph_pie installed to see the chart.
// Change the variable below to reflect the location of jpgraph relative
// to Snort Report, for example "../jpgraph/", etc.
define("JPGRAPH_PATH", "../jpgraph/src/");

// Path to external utilities
// Enter the correct path (including the binary) to nmap and nbtscan if you have them installed
// You can also include switches for each binary (see nmap)
define("NMAP_PATH", "/usr/bin/nmap -v");
define("NBTSCAN_PATH", "/usr/bin/nbtscan");


// Custom microtiming functions for profiling pages - available from http://improbable.org/chris/software/profiling.phps  
define("PROFILING", false);
if (PROFILING) {
	require_once("profiling.phps");
}

//
// YOU DON'T NEED TO MODIFY ANYTHING UNDER THIS LINE
//

// Open a connection to the database
require_once("DB.php");
$db = new DB;
$db->setinst($server);
$db->setuser($user);
$db->setpass($pass);
$db->dbname($dbname);
$db->persist();
$conn = $db->connect();

define("FULL_DATETIME_FORMAT", "Y-m-d H:i:s");

set_time_limit(1800);

require_once("info-retrieval.php");
?>
