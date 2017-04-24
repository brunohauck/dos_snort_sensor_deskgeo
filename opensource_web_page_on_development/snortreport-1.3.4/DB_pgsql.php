<?php
	/*
	PostgreSQL module Copyright 2001 Enrico Scholz (Enrico.Scholz@cvg.de)	
	DB Abstraction layer Copyright 2001 Jason Costomiris
	Distributed under the terms of the GPL v2
	*/

	class DB extends DB_base {
		// Connect or pconnect.
		function connect(){
			$str = 'user='.$this->user.' password='.$this->pass.' dbname='.$this->dbname;

			if ($this->instance) {
				$str = $str.' host='.$this->instance;
			}
			
			if($this->persist){
				$this->conn = pg_pconnect($str);
			} else {
				$this->conn = pg_connect($str);
			}
			
			$str = pg_errormessage();
			if($str) { die($str); }
			return($this->conn);
		}

		// close
		function close(){
			if($this->persist) {
				$ret = 1;
			} else {
				$ret = pg_close($this->conn);
			}
			return($ret);
		}

		// query
		function query($query){
			#print $query."<br>\n";
			$result = pg_exec($this->conn, $query);
			$str = pg_errormessage($this->conn);
			if($str) { die($str); }

			return array($result, 0);
		}
		
		// numrows - modified this function to take input 
		//           so you can do nested queries by passing
		//           returned object from query function
		//           Modified:  6/30/2000 JMc
		function numrows($result){
			$numrows = pg_numrows($result[0]);
			return($numrows);
		}

		// affected rows
		//function affrows(){
		//	$this->affrows = pg_affected_rows($this->result);
		//	return($this->affrows);
		//}

		// seek
		function seek(&$result, $row){
			$max = pg_numrows($result[0]);
			if ($row>=$max) return FALSE;

			$result[1] = $row;	
			return TRUE;
		}

		function nextIdx(&$result) {
			$idx = $result[1];
			if ($idx>=pg_numrows($result[0])) { return NULL; }

			$result[1] = $idx+1;
			return $idx;
		}


		// fetch array - modified this function to take input 
		//               so you can do nested queries by passing
		//               returned object from query function
		//               Modified:  7/3/2000 JMc
		function farray(&$result){
			$idx = $this->nextIdx($result);
			if ($idx===NULL) { return FALSE; }

			return pg_fetch_array($result[0], $idx);
		}

		// fetch object - modified this function to take input 
		//                so you can do nested queries by passing
		//                returned object from query function
		//                Modified:  6/29/2000 JMc
		function fobject(&$result){
			$idx = $this->nextIdx($result);
			if ($idx===NULL) { return FALSE; }

			return pg_fetch_object($result[0], $idx);
		}

		// result
		function result($result, $index){
			$value = pg_result($result[0], $index, 0);
			return($value);
		}
		
		// free
		function free($result){
			$free = pg_freeresult($result[0]);
			return($free);
		}

                function timeSinceEpoch($col) {
                        return 'EXTRACT(EPOCH FROM '.$col.')';
                }

                function timestamp($time) {
                        return $time;
                }

                function dateFormat($col, $format) {
                        return "to_char($col, '$format')";
                }
	}
?>
