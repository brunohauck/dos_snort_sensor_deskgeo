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

// print page gen time info
$finishGenTime = date("F j, Y, H:i:s");
?>
<br><br>
<span class="timeblock">
<b>Page begun:</b> <?=$startGenTime?><br>
<b>Page finished:</b> <?=$finishGenTime?><br>


<!-- output footer -->

<table width="700" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="700" valign="top" align="center" class="smalltitle">
<br><br><br><br><?=$srVersion?><br>Copyright 2000-2013, <a href="http://www.symmetrixtech.com">Symmetrix Technologies, LLC.</a></td>
</tr></table>

</body>
</html>

