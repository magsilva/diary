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

Copyright (C) 2003 Marco Aurelio Graciotto Silva <magsilva@gmail.com>
*/

require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/common.php');
foreach (glob(dirname(__FILE__) . '/objects/*.class.php') as $file) {
	require_once($file);
}

/**
* A diary's abstract class. This is a simple diary. It has a name,
* description, author and, of course, entries. Each entry can have public and
* private sections, with anything within them.
* 
* Txt  based diary. The entries are plain text files, each one named with the
* date in the format YYYY-MM-DD. The first line is its title. Private data's
* line starts with a "#".
*/
class diary
{
	/**
	 * Absolute base dir.
	 */
	private $base_dir;

	/**
	 * Base URL.
	 */
	private $url;
	
	/**
	 * Title.
	 */
	private $title;
	
	/**
	 * Description.
	 */
	private $description;
	
	/**
	 * Author.
	 */
	private $author;
	
	/**
	 * Enable/disable the visualization of private records.
	 */
	private $enable_private;
	
	/**
	 * Password to access private records.
	 */
	private $password;
	
	/**
	 * Full type.
	 */
	private $type;
	
	/**
	 * Tags directory.
	 */
	private $tags;
	
	private function set_author($author)
	{
		$this->author = $author;
	}
	
	public function get_author()
	{
		return $this->author;
	}
	
	
	private function set_title($title)
	{
		$this->title = $title;
	}
	
	public function get_title()
	{
		return $this->title;
	}

	private function set_description($description)
	{
		$this->description = $description;
	}
	
	public function get_description()
	{
		return $this->description;
	}

	private function set_base_dir($base_dir)
	{
		$this->base_dir = dir($base_dir);
	}
	
	public function get_base_dir()
	{
		return $this->base_dir;
	}

	private function set_url($url)
	{
		$this->url = $url;
	}
	
	public function get_url()
	{
		return $this->url;
	}
	
	
	private function set_password($password)
	{
		$this->password = $password;
	}
	
	public function is_password_correct($password)
	{
		if ($this->password === $password) {
			$this->enable_private = true;
			return true;
		} else {
			$this->enable_private = false;
			return false;
		}
	}
		
	/**
	* Diary constructon. Reads the configuration and creates the entries.
	*/
	public function __construct()
	{
		$this->set_author(AUTHOR);
		$this->set_title(NAME);
		$this->set_description(DESCRIPTION);
		$this->set_base_dir(BASE_DIR);
		$this->set_password(PASSWORD);
		$this->set_url(BASE_URL);

		$this->type = array();

		$this->tags = array();
	
		$this->read_objects($this->base_dir);
	}
	
	private function tag($tag, $obj)
	{
		$tag = strval($tag);
		if (!array_key_exists($tag, $this->tags) || $this->tags[$tag] == NULL) {
			$this->tags[$tag] = array();
		}
		$this->tags[$tag][] = $obj;
	}
	
	private function type($name, $obj)
	{
		$name = strval($name);
		if (!array_key_exists($name, $this->type) || $this->type[$name] == NULL) {
			$this->type[$name] = array();
		}
		$this->type[$name][] = $obj;
	}
	
	public function read_objects($dir)
	{
		$years = array();
		static $depth = -1;
		static $year;
		static $month;
		static $day;
		
		$dir->rewind();
		while (false !== ($obj_filename = $dir->read())) {
			if ($obj_filename != "." && $obj_filename != "..") {
				$full_filename = $dir->path . "/" . $obj_filename;
				$this->type($obj_filename, $full_filename);
				
				if (is_file($full_filename)) {
					$this->tag('File', $full_filename);
				}
								
				if (is_dir($full_filename)) {
					$this->tag('Directory', $full_filename);
				}
								
				if (ereg('.jpg$', $obj_filename) || ereg('.png$', $obj_filename) || ereg('.gif$', $obj_filename)) {
					$this->tag('Picture', $full_filename);
				}
				
				if (ereg('.pdf$', $obj_filename) || ereg('.odt$', $obj_filename)) {
					$this->tag('Document', $full_filename);
				}
				
				if (ereg('.rpm$', $obj_filename)) {
					$this->tag('RpmPackage', $full_filename);
				}
				
				if (ereg('.deb$', $obj_filename)) {
					$this->tag('DebPackage', $full_filename);
				}
				
				if (ereg('.tar.bz2$', $obj_filename) || ereg('.tar.gz$', $obj_filename)) {
					$this->tag('Tarball', $full_filename);
				}

				if ($depth == -1 && ereg('([0-9]{4})', $obj_filename, $temp)) {
					if (is_dir($full_filename)) {
						$year = $obj_filename;
						$depth++;	
						$this->read_objects(dir($full_filename));
						$depth--;
					} else {
						if (ereg(GOAL_EXTENSION_PATTERN, $obj_filename)) {
							$obj = new year_goals($temp[0], $full_filename);
							$this->tag('YearGoals', $obj);
							$this->tag($temp[0], $obj);
						}
							
						if (ereg(LOG_EXTENSION_PATTERN, $obj_filename)) {
							$obj = new yearly_log($temp[0], $full_filename);
							$this->tag('YearlyLog', $obj);
							$this->tag($temp[0], $obj);
						}
						
					}
				}
								
				if ($depth == 0 && ereg('([0-9]{2})', $obj_filename)) {
					if (is_dir($full_filename)) {
						$month = $obj_filename;
						$depth++;	
						$this->read_objects(dir($full_filename));
						$depth--;
					}
				}
				if ($depth == 0 && ereg('([0-9]{4})([0-9]{2})', $obj_filename, $temp)) {
					if ($temp[0] == $year) {
						if (ereg(GOAL_EXTENSION_PATTERN, $obj_filename)) {
							$obj = new month_goals($temp[1], $temp[0], $full_filename);
							$this->tag('MonthGoals', $obj);
							$this->tag($temp[1] . $temp[0], $obj);
						}
	
						if (ereg(LOG_EXTENSION_PATTERN, $obj_filename)) {
							$obj = new monthly_log($temp[1], $temp[0], $full_filename);
							$this->tag('MonthlyLog', $obj);
							$this->tag($temp[1] . $temp[0], $obj);
						}
					}
				}
				
				if ($depth == 1 && ereg('([0-9]{2})', $obj_filename)) {
					if (is_dir($full_filename)) {
						$day = $obj_filename;
						$depth++;	
						$this->read_objects(dir($full_filename));
						$depth--;
					}
				}

				if ($depth == 1 && preg_match('/' . DATE_PATTERN . '/', $obj_filename, $temp)) {
					if ($temp[1] == $year && $temp[2] == $month) {
						if (ereg(GOAL_EXTENSION_PATTERN, $obj_filename)) {
							$obj = new day_goals($temp[2], $temp[1], $temp[0], $full_filename);
							$this->tag('DayGoals', $obj);
							$this->tag($temp[2] . $temp[1] . $temp[0], $obj);
						}
						if (preg_match('/' . DAILY_LOG_PATTERN . '/', $obj_filename, $temp)) {
							$obj = new daily_log($temp[3], $temp[2], $temp[1], $full_filename);
							$this->tag('DaylyLog', $obj);
							$this->tag($temp[1] . $temp[2] . $temp[3], $obj);
						}
					}
				}
			}
		}
		$dir->close();
	}
	
	public function slice($tag_slice)
	{
		$result = array();
		foreach ($this->tags as $tag => $values) {
			$count= preg_match($tag_slice, $tag);
			if ($count !== FALSE && $count > 0) {
				if (is_array($values)) {
					foreach($values as $value) {
						$result[] = $value;
					}
				} else {
					$result[] = $values;
				}
			}
		}
		
		return $result;
	}


	public function get_data($slice = NULL) {
		$data = "";

		if (isset($slice) && ! empty($slice)) {
			$objects =& $this->slice($slice);
		} else {
			$objects = array();
			$min_count = 7;
			$days_increment = 7;
			$max_days = 3000;
			$days = 1 * $days_increment;
			while (count($objects) < $min_count && $days < $max_days) {
				/**
				* Check if a specified data has been request or it's to show last week's
				* activity.
				*/
				$end_time = time();
				$start_time = $end_time - ($days * 24 * 60 * 60);
				$slice = '/(';
				for ($i = $start_time; $i < $end_time; $i += (24 * 60 * 60)) {
					$slice .= strftime('%Y%m%d', $i);
					$slice .= '|';
				}
				$slice = rtrim($slice, '|');
				$slice .= ')/';

				$objects =& $this->slice($slice);
				$days += $days_increment;
			}
		}

		$entries = array();
		$class = new ReflectionClass('daily_log');
		foreach ($objects as $object) {
			if ($class->isInstance($object)) {
				$entries[$object->get_id()] = $object;
			}
		}
		arsort($entries);


		foreach ($entries as $entry) {
			$data .= "<div class='entry'>\n";
			$data .= "\t<h2><a name='" . $entry->get_id() . "' href='#" . $entry->get_id() . "'>" . ucfirst(strftime("%e/%m/%G", $entry->get_date())) . ' - ' . $entry->get_title() . "</a></h2>\n";
			if ($this->enable_private) {
				$contents = $entry->get_everything();
			} else {
				$contents = $entry->get_public();
			}

			$ignore_nl = FALSE;
			foreach ($contents as $content) {
				$content = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\">\\0</a>", $content);
				/*
				foreach ($pictures as $picture) {
					$content = ereg_replace( basename( $picture ), "<div class=\"
						img\" align=\"center\"><img src=\"" . $picture . "\" /></div>",
						$content);
				}
				foreach ( $objects as $object ) {
					$content = ereg_replace( basename( $object ), "<a href=\"" .
						$object . "\">" . basename( $object ) . "</a>", $content );
				}
				*/
				if (stristr(trim($content), '<pre>') != FALSE) {
					$ignore_nl = TRUE;
				}
				if (stristr(trim($content), '</pre>') != FALSE) {
					$ignore_nl = FALSE;
				}
				if (!$ignore_nl) {
					$data .= "\t\t<p class='content'>" . $content . "</p>\n";
				} else {
					$data .= $content . "\n";
				}
			}
			$data .= "</div>\n\n";
		}

		return $data;
	}
}
?>
