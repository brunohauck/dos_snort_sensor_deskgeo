<?

// info-retrieval.php contributed to Snort Report 1.04
// August 13, 2001
// Copyright (C) 2001 Chris Adams
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
	
	
	// Information retrieval functions
	
	function reference_url($System, $ID) {
		// Given a reference system and ID, it returns the URL to retrieve that advisory
		// The list below was borrowed from SnortSnart's SnortFileInput.pm
	
		switch ($System) {
//                      case 'arachnids':
//                              $ID = preg_replace("/^0+/", "", $ID);
//                              return "http://whitehats.com/IDS/IDS$ID";
//                              Whitehats.com doesn't exist any longer.
			case 'bugtraq':
				return "http://www.securityfocus.com/bid/$ID";
			case 'cve':
				return "http://cve.mitre.org/cgi-bin/cvename.cgi?name=$ID";
			case 'mcafee':
				return "http://vil.nai.com/vil/dispVirus.asp?virus_k=$ID";
			case 'nessus':
				return "http://www.nessus.org/plugins/index.php?view=single&id=$ID";
			case 'url':
				return "http://$ID";
		}
		return false;
	}

?>
