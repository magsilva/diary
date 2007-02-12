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
define('GOAL_EXTENSION', 'goal$');


/**
* A diary's entry. This is a simple entry, with a date, title, public and private date.
*/
abstract class goals extends log
{
	public function __construct($filename)
	{
		parent::__construct($filename);
	}

	private function read_data()
	{
		$file = fopen($this->filename, 'r');
		$this->title = trim(fgets($file));
		while (!feof($file)) {
			$content = trim(fgets($file));
			if (strlen($content) > 0) {
				$record = array();

				$ok = FALSE;
				if (strripos($content, "ok") !== FALSE) {
					$record['status'] = FALSE;
				} else {
					$record['status'] = TRUE;
					$content = preg_replace('/\.ok$/i', '', $content, 1); 
				}
				
				if ($content{0} == "#") {
					$content = trim(ltrim($content, "\#"));
					$content['private'] = TRUE;
				} else {
					$content['private'] = FALSE;
				}
					
				$record['content'] = $content;
				
				$this->records[] = $record;
			}
		}
	}

	public function get_status($goal)
	{
		foreach ($this->records as $record) {
			if ($record['content'] == $goal) {
				return $record['status'];
			}
		}
		return false;
	}
}

?>