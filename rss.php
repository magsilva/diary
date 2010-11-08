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

require_once(dirname(__FILE__) . '/diary.php');
require_once(dirname(__FILE__) . '/libs/rss2writer/rss2writer.php');

$diary = new Diary();
$rss = new RSS2Writer($diary->get_url(), $diary->get_title(), $diary->get_description());
$rss->useModule('dc', 'http://purl.org/dc/elements/1.1/');

$objects = array();
$min_count = 7;
$max_days = 3000;
$days_increment = 7;
$days = $days_increment;
while (count($objects) < $min_count && $days < $max_days) {
	$end_time = time();
	$start_time = $end_time - ($days * 24 * 60 * 60);
	$slice = '/(';
	for ($i = $start_time; $i < $end_time; $i += (24 * 60 * 60)) {
		$slice .= strftime('%Y%m%d', $i);
		$slice .= '|';
	}
	$slice = rtrim($slice, '|');
	$slice .= ')/';
	$objects =& $diary->slice($slice);
	$days += $days_increment;
}

$entries = array();
$class = new ReflectionClass('daily_log');
foreach ($objects as $object) {
	if ($class->isInstance($object)) {
		$entries[$object->get_date()] = $object;
	}
}
arsort($entries);

foreach ($entries as $entry) {
	$rss->addItem($diary->get_url() . '&slice=' . $entry->get_id(), $entry->get_title(), $entry->get_content(), array('dc:date' => date('Y-m-d\TH:m:s\Z', $entry->get_date())));
}
$rss->output();
?>
