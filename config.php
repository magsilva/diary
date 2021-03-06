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

/**
* Diary's name, used as title.
*/
define('NAME', 'Diary');

/**
* Diary's author.
*/
define('AUTHOR', 'Marco Aur�lio Graciotto Silva');

/**
* Diary's description.
*/
define('DESCRIPTION', 'Notes from a cranky nerd');

/**
* Directory with diary's entries.
*/
define('BASE_DIR', dirname(__FILE__) . '/data');

/**
* Password to be used.
*/
define('PASSWORD', '');

/**
 * Root URL.
 */
define('BASE_URL', 'http://www.ironiacorp.com/index.php');

date_default_timezone_set('America/Sao_Paulo');
?>
