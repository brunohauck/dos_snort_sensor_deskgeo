<?php
	header("Pragma: no-cache");
	header("Cache-Control: no-cache, must-revalidate, max_age=0"); 
	header("Expires: 0");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<!--
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
-->

<head>
<link rel="stylesheet" href="default.css">
<STYLE><!--
A.ssmItems:link		{color:black;text-decoration:none;}
A.ssmItems:hover	{color:black;text-decoration:none;}
A.ssmItems:active	{color:black;text-decoration:none;}
A.ssmItems:visited	{color:black;text-decoration:none;}
//--></STYLE>
<SCRIPT SRC="ssm.js" language="JavaScript1.2"></SCRIPT>
<SCRIPT SRC="ssmItems.js" language="JavaScript1.2"></SCRIPT>
<title><?=$title?></title>
</head>
<body>
<?php

// include variables
require_once('srconf.php'); 

flush(); // Speed perceived load time

// Begin time for generation of report
$startGenTime = date("F j, Y, H:i:s");

?>
