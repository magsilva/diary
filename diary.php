<?php

require_once( "diary_entry.php" );

/**
* A diary's abstract class. This is a simple diary. It has a name, description, author and, of course,
* entries. Each entry can have public and private sections, with anything inside it.
*/
class diary {
	var
		$name,		// Name.
		$description,	// Description.
		$author,	// Author.
		$enable_private,// Enable/disable the visualization of private records.
		$entries,	// Diary's entries.
		$password; // Diary's password.
		
	function add_entry( $entry ) {
		$this->entries[ $entry->date ] = $entry;
	}

	function remove_entry( $date ) {
		unset( $this->entries[ $date ] );
	}

	function get_entry( $date ) {
		return $entry = $this->entries[ $date ];
	}

	function get_entry_count() {
		return sizeof( $this->entries );
	}

	function get_all_entries() {
		return $this->entries;
	}

	function diary() {
		$this->enable_private = 0;
		$entries &=  array();
	}

	function to_string() {
		$result = $this->name . "," . $this->description . "," . $this->author . "," . $enable_private . "\n";
		foreach ( $this->entries as $entry ) {
			$result .= $entry->to_string();
		}
		return $result;
	}
}
?>
