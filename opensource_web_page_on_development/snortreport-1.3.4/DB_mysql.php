<?php
	/*
	
	DB Abstraction layer Copyright 2001 Jason Costomiris
	Distributed under the terms of the GPL v2

	*/

	class DB extends DB_base {
		
		// Connect or pconnect.
		function connect(){
			if($this->persist){
				$this->conn = 
					mysql_pconnect($this->instance, $this->user, $this->pass);
			} else {
				$this->conn = 
					mysql_connect($this->instance, $this->user, $this->pass);
			}
			mysql_select_db($this->dbname, $this->conn);
			$str = mysql_error();
			if($str) { die($str); }
			return($this->conn);
		}

		// close
		function close(){
			if($this->persist) {
				$ret = 1;
			} else {
				$ret = mysql_close($this->conn);
			}
			return($ret);
		}

		// query
		function query($query){
			$this->result = mysql_query($query);
			$str = mysql_error();
			if($str) { die($str); }
			return($this->result);
		}
		
		// numrows - modified this function to take input 
		//           so you can do nested queries by passing
		//           returned object from query function
		//           Modified:  6/30/2000 JMc
		function numrows($result){
			$this->numrows = mysql_num_rows($result);
			return($this->numrows);
		}

		// affected rows
		function affrows(){
			$this->affrows = mysql_affected_rows($this->result);
			return($this->affrows);
		}

		// seek
		function seek($result, $row){
			$seek = mysql_data_seek($result, $row);
			return($seek);
		}

		// fetch object - modified this function to take input 
		//                so you can do nested queries by passing
		//                returned object from query function
		//                Modified:  6/29/2000 JMc
		function fobject($result){
			$object = mysql_fetch_object($result);
			return($object);
		}

		// fetch array - modified this function to take input 
		//               so you can do nested queries by passing
		//               returned object from query function
		//               Modified:  7/3/2000 JMc
		function farray($result){
			$array = mysql_fetch_array($result);
			return($array);
		}

		// result
		function result($result, $index){
			$value = mysql_result($result, $index);
			return($value);
		}
		
		// free
		function free($result){
			$free = mysql_free_result($result);
			return($free);
		}

		function timeSinceEpoch($col) {
			return "UNIX_TIMESTAMP($col)";
		}
		
		function timestamp($time) {
			return "FROM_UNIXTIME($time)";
		}
		
		function dateFormat($col, $format) {
			return "DATE_FORMAT($col, \"$format\")";
		}

		function quote($str) {
			return "\"$str\"";
		}

	}

?>
