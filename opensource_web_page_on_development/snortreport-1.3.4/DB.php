<?php
	
	/*
	
	DB Abstraction layer Copyright 2001 Jason Costomiris
	Distributed under the terms of the GPL v2
	
	*/
	class DB_base {
		// get server/instance
		function setinst($instance){
			$this->instance = $instance;
		}

		// get username
		function setuser($user){
			$this->user = $user;
		}

		// get username
		function setpass($pass){
			$this->pass = $pass;
		}

		// get database name
		function dbname($dbname){
			$this->dbname = $dbname;
		}

		// Use persistent connections?
		// Only call this if you want persistent connections
		function persist(){
			$this->persist = 1;
		}

		// Returns the time since epoch in seconds of `col'
        function timeSinceEpoch($col) {
        return $col;
        }

        // Returns a string which can be used to compare a timestamp
        // with the given time
        function timestamp($time) {
        return $time;
        }

        // Returns the function-call which transforms the column `col'
        // into the given date-format `format'
        function dateFormat($col, $format) {
        }

        // Quotes the given string so it can be used in a query
        function quote($str) {
        return "'$str'";
        }

	}
	
	$file = join("", array("DB_", $dbtype, ".php"));
	require($file);
?>
