<?php

/**
* A diary's entry. This is a simple entry, with a date, title, public and private date.
*/
class diary_entry {
	var
		$date,		// Date.
		$title,		// Title
		$public,	// Public data container (array).
		$private,	// Private data container (array).
		$counter;	// Counter (used for sort the data).

	/**
	* Create a new diary entry (must have a date and a title).
	*/
	function diary_entry( $date, $title ) {
		$this->counter = 1;
		$this->date = $date;
		$this->title = $title;
		$this->public = array();
		$this->private = array();
	}

	function get_date() {
		return ucfirst( strftime( "%e/%m/%G", $this->date ) );
	}

	function get_id() {
		return ucfirst( strftime( "%G%m%d", $this->date ) );
	}


	function post_add() {
		$this->counter++;
	}
	
	/**
	* Add public data. 
	*/	
	function add_public( $data ) {
		$this->public[ $this->counter ] = $data;
		$this->post_add();
	}

	/**
	* Add private data.
	*/
	function add_private( $data ) {
		$this->private[ $this->counter ] = $data;
		$this->post_add();
	}

	/**
	* Get public data.
	*/
	function get_public() {
		return $this->public;
	}

	/**
	* Get private data.
	*/
	function get_private() {
		return $this->private;
	}

	/**
	* Get all data, ordered.
	*/
	function get_everything() {
		$everything = array_merge( $this->public, $this->private );
		ksort( $everything );
		return $everything;
	}

	function get_pictures() {
		$pictures = array();
		$pictures = array_merge( $pictures, glob( $this->get_id() . "/*.jpg" ) );
		$pictures = array_merge( $pictures, glob( $this->get_id() . "/*.png" ) );
		$pictures = array_merge( $pictures, glob( $this->get_id() . "/*.gif" ) );
		return $pictures;
	}

	function get_objects() {
		$objects = array();
		$objects = array_merge( $objects, glob( $this->get_id() . "/*.pdf" ) );
		$objects = array_merge( $objects, glob( $this->get_id() . "/*.tar.bz2" ) );
		$objects = array_merge( $objects, glob( $this->get_id() . "/*.rpm" ) );
		return $objects;
	}

	/**
	* Dump the entry.
	*/
	function to_string() {
		$result = $date . "," . $title . "," . $counter . "\n";
		$everything = $this->get_everything();
		for ( $i = 1; $i < $this->counter; $i++ ) {
			$result .= "\t";
			if ( array_key_exists( $i, $this->private ) ) {
				$result .= "[Private] ";
			} else {
				$result .= "[Public] ";
			}
			$result .= $i . "," . $everything[ $i ] . "\n";
		}
		return $result;
	}
}

?>
