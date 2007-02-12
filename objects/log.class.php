<?php
/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Copyright (C) 2007 Marco Aurelio Graciotto Silva <magsilva@gmail.com>
*/

require_once(dirname(__FILE__) . '/object.class.php');

/**
* File extension to be used.
*/
define('LOG_EXTENSION_PATTERN', '(\.txt$)');


/**
* A diary's entry. This is a simple entry, with a date, title, public and private date.
*/
abstract class log extends object
{
	protected $filename;
	
	protected $title;
	
	protected $date;
	
	/**
	 * Every record is an array, with 'content', 'status' and 'private'.
	 */
	protected $records;
	
	public function __construct($filename)
	{
		parent::__construct($filename);
		$this->date = filectime($this->filename);
		$this->records = array();
		$this->read_data();
	}

	private function read_data()
	{
		$file = fopen($this->filename, 'r');
		$this->title = trim(fgets($file));
		$start_new_paragraph = TRUE;
		$count = 0;
		
		while (!feof($file)) {
			$content = trim(fgets($file));
			if (strlen($content) > 0) {
				if ($start_new_paragraph) {
					$count++;
					$this->records[$count] = array();
					$start_new_paragraph = FALSE;
				}

				$record =& $this->records[$count];
				if ($content[0] == "#") {
					$content = trim(ltrim($content, "\#"));
					$record['private'] = TRUE;
				} else {
					$record['private'] = FALSE;
				}
				
				$content .= " ";
				$record['content'] .= $content;
			} else {
				$start_new_paragraph = TRUE;
			}
		}
	}

	public function get_date()
	{
		return ucfirst(strftime("%e/%m/%G", $this->date));
	}
	
	public function get_id()
	{
		return ucfirst(strftime("%G%m%d", $this->date));
	}

	public function get_title()
	{
		return $this->title;
	}

	/**
	* Get public data.
	*/
	public function get_public()
	{
		$log = array();
		foreach ($this->records as $record) {
			if (! $record['private']) {
				$log[] = $record['content'];
			}
		}
		return $log;
	}

	/**
	* Get private data.
	*/
	public function get_private()
	{
		$log = array();
		foreach ($this->records as $record) {
			if ($record['private']) {
				$log[] = $record['content'];
			}
		}
		return $log;
	}

	/**
	* Get all data, ordered.
	*/
	public function get_everything()
	{
		$log = array();
		foreach ($this->records as $record) {
			$log[] = $record['content'];
		}
		return $log;
	}
}

?>
