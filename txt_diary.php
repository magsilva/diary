<?php

require_once( "diary.php" );
require_once( "txt_diary_config.php" );

/**
* Txt based diary. The entries are plain text files, each one named with the date in the format YYYY-MM-DD.
* The first line is its title. Private data's line starts with a "#".
*/
class txt_diary extends diary {
	var
		$base_dir;	// Relative or absolute base dir.
		
	/**
	* Diary constructon. Reads the configuration and creates the entries.
	*/
	function txt_diary() {
		$this->author = AUTHOR;
		$this->name = NAME;
		$this->description = DESCRIPTION;
		$this->enable_private = ENABLE_PRIVATE;
		$this->base_dir = dir( BASE_DIR );
		$this->password = PASSWORD;

		while ( false !== ( $entry_date = $this->base_dir->read() ) ) {
			if ( $entry_date != "." && $entry_date != ".." && ereg( FILE_EXTENSION , $entry_date ) ) {
      	$entry_file = fopen( $this->base_dir->path . "/" . $entry_date, "r" );
				$entry_title = trim( fgets( $entry_file ) );
				$entry_date = strtok( $entry_date, "." );
				$entry = new diary_entry( strtotime( $entry_date ), $entry_title );
				while ( !feof( $entry_file ) ) {
					$entry_data = trim( fgets( $entry_file ) );
					if ( strlen( $entry_data ) > 0 ) {
						if ( $entry_data[ 0 ] != "#" ) {
							$entry->add_public( $entry_data );
						} else {
							$entry_data = ltrim( $entry_data, "\# " );
							$entry->add_private( $entry_data );
						}
					}
				}
				$this->add_entry( $entry ); 
			}
		}
	}
}
?>
