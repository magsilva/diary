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


/**
* A diary's entry. This is a simple entry, with a date, title, public and private date.
*/
abstract class object
{
	protected $filename;
	
	public function __construct($filename)
	{
		$this->filename = realpath($filename);
	}
	
	public function get_last_modification_date()
	{
		return filemtime($this->filename);
	}
}


?>