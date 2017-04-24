<?php


// info-retrieval.php contributed to Snort Report 1.1
// November 8, 2001
// Copyright (C) 2001 Enrico Scholz <enrico.scholz@informatik.tu-chemnitz.de>
//  
// This module is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; version 2 of the License.


/** Encapsulates a timespan. Its boundaries can be given in various formats:
 *  - as seconds since epoch
 *  - as the special value '-1' which means the epoch at start-time and now 
 *    at the end-time
 *  - as a string consisting of a numeric value followed by a single char
 *    symbolizing a time-unit. Possible values are:
 *    's' - seconds since epoch, 'y' - year, 'm' - month, 'w' - week
 *    'd' - days, 'H' - hours, 'M' - minutes, 'S' - seconds
 *
 *    With the exception of 's', such values are describing the time before
 *    now. E.g. "timespan('6H', '3H')" means the timespan beginning 6 hours 
 *    before and ending 3 hours before now.
 *  
 * This class can express the timespan in some formats: in SQL, as part of
 * an URL-query and in its native UNIX-format. */
class timespan {
	function timespan($start, $end) {
		$this->start = $this->parseTime($start);
		$this->end   = $this->parseTime($end);
	}

	/** Returns the lower boundary of the timespan as seconds since 
         *  epoch. Special value of '-1' is expressed as '0'. */
	function getStart() {
		if ($this->start===NULL) { return 0; }
		else                     { return $this->start; }
	}

	/** Returns the higher boundary of the timespan as seconds since 
         *  epoch. Special value of '-1' is expressed as 'time()'. */
	function getEnd() {
		if ($this->end===NULL)   { return time(); }
		else                     { return $this->end; }
	}

	/** Returns the timespan as part of a URL query. The boundaries 
         *  are transformed into seconds since epoch as described at the
         *  getStart()/getEnd() methods and tied with the variablenames
         *  given as params. 
         *
         *  A sample result would be 
         *         'beginTime=1001988375&endTime=1002593182'
         *  if called as 'getURL("beginTime", "endTime"). */
	function getURL($begin_name, $end_name) {
		$result = $begin_name."=".$this->getStart()."&".
                          $end_name."=".$this->getEnd();

		return $result;
	}

        /** Returns the timespan as part of the WHERE clause of an SQL
         *  statements. This clause tells if the $fieldname argument is
         *  within the encapsulated range. If one boundary symbolizes an
         *  unlimited value (-1) it will be omitted. If the class was 
         *  constructed with ('-1','-1') which means everytime, this 
         *  method returns '(1=1)'. Else it returns a bracketed expression,
         *  e.g. 
         *   (event.timestamp>=1002551376 AND event.timestamp<1002587376) */
	function getSQL($db, $fieldname) {
		$result = '';
		$delim  = '';

		if ($this->start!==NULL) {
			$result = $result.$delim.$fieldname.">=".$db->timestamp($this->start);
			$delim  = ' AND ';
		}

		if ($this->end!==NULL) {
			$result = $result.$delim.$fieldname. "<".$db->timestamp($this->end);
		}

		if ($result=='') { $result = '(1=1)'; }
		else             { $result = "($result)"; }

		return $result;
	}

	/** Parses a string and transforms it into a time. It returns either time since
         *  epoch or the special value NULL. */
	function parseTime($time)
	{
	        $val = intval($time);

		if ("$val"===$time or $val===$time) { $mod = 's'; }
		else {
			$val = substr($time,0,-1);
        	        $mod = substr($time,-1);
	        }

	        if ($val==intval($val)) {
        	        $val = intval($val);
                	switch ($mod) {
                        	case 's':  break; // special; seconds since epoch

	                        case 'y':  $val = $val*365*24*3600; break;      // year
	
        	                case 'm':  $val = $val*4;                       // month
                	        case 'w':  $val = $val*7;                       // week
                        	case 'd':  $val = $val*24;                      // day
	                        case 'H':  $val = $val*60;                      // hour
        	                case 'M':  $val = $val*60;                      // minute
                	        case 'S':  break;                               // seconds

                        	case 'm':  $val = $val*3600*24*28; break;       // month

	                        default:   $val = 0; $mod = 's'; break;
        	        }
	        }

        	if     ($mod!='s') { $val = time()-$val; }
	        elseif ($val==-1)  { $val = NULL; }

        	return $val;
	}
}

?>
