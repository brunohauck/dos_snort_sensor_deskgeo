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

// TODO: would it be faster to figure out how to make ip2long and long2ip sub here or to use the aton/ntoa functions in MySQL?

function dec2IP ($dec) {
	$hex = dec2hex ($dec);
	if (strlen($hex) == 7) $hex = "0".$hex;
	$one = hexdec(substr($hex,0,2));
	$two = hexdec(substr($hex,2,2));
	$three = hexdec(substr($hex,4,2));
	$four = hexdec(substr($hex,6,2));
	$ip = $one.".".$two.".".$three.".".$four;
	return ($ip);
}

function ip2dec ($ip)
{
  $dec = 0.0;

  $val = explode('.', $ip);
  for ($i=0; $i<count($val); $i++)
  {
   $dec = ($dec *256.0);
   $dec+=($val[$i]*1.0);
  }
  return $dec;
}


function dec2hex($dec) { 
	if($dec > 2147483648) { 
		$result = dechex($dec - 2147483648); 
		$prefix = dechex($dec / 268435456); 
		$suffix = substr($result,-7); 
		$hex = $prefix.str_pad($suffix, 7, "0000000", STR_PAD_LEFT); 
	} 
	else { 
		$hex = dechex($dec); 
	} 
	$hex = strtoupper ($hex);
	return($hex); 
}

function hex2str($hex) {
	$str = "";

	$i = 0;
    while ($i < strlen($hex)) {
		$tmp = hexdec(substr($hex,$i,2));

		if ($tmp < 32) $tmp = 46;			//protect against control characters
		if ($tmp > 126) $tmp = 46;
		$tmp = chr($tmp);
		if ($tmp == "<") $tmp = "&lt";		//protect against HTML payloads
		if ($tmp == ">") $tmp = "&gt";

		$str .= $tmp;
		$i += 2;
	}
	return $str;
}

function convertTCPFlags($decdata){
	$str = "";
	$binstr = decbin ($decdata);
	$binstr = str_pad($binstr, 8, "0", STR_PAD_LEFT);  
	$i = 8;
    while ($i > 2) {
		$tmp = substr($binstr,$i-1,1);
		switch ($i) {
			case 3:
				if ($tmp == 0) $str = "*".$str;
				else $str = "U".$str;
				break;
			case 4:
				if ($tmp == 0) $str = "*".$str;
				else $str = "A".$str;
				break;
			case 5:
				if ($tmp == 0) $str = "*".$str;
				else $str = "P".$str;
				break;
			case 6:
				if ($tmp == 0) $str = "*".$str;
				else $str = "R".$str;
				break;
			case 7:
				if ($tmp == 0) $str = "*".$str;
				else $str = "S".$str;
				break;
			case 8:
				if ($tmp == 0) $str = "*".$str;
				else $str = "F".$str;
				break;
		}

		$i--;
	}
	$str = "**".$str;
	return ($str);

}
?>
