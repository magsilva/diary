<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">

<?php
	require_once('diary.php');
	$diary = new diary();
?>


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta name="author" content="<?php echo $diary->get_author(); ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title><?php echo $diary->get_title(); ?></title>
	<link href="diary.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php
	echo "<h1 class=\"name\">" . $diary->get_title() . "</h1>\n\n";
	echo "<p class=\"description\">" . $diary->get_description() . "</p>\n\n\n";
?>

<div align="right">
<form action="<?php echo $_SERVER[ "PHP_SELF" ] ?>" method="post" title="Enter the password to access the private data">
	<input type="password" name="password" />
	<input value="Go!" name="go_private" type="submit" />
</form>
</div>

<?php
	/**
	* Check if private data must be shown (check the password).
	*/
	$show_private = FALSE;
	if (isset($_REQUEST['password'])) {
		if ($diary->is_password_correct($_REQUEST['password'])) {
			$show_private = TRUE;
		}
	}

	if (isset($_REQUEST['slice'])) {
		$slice = $_REQUEST['slice'];
		$objects =& $diary->slice($slice);
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

			$objects =& $diary->slice($slice);
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
		echo "<div class='entry'>\n";
		echo "\t<h2><a name='" . $entry->get_id() . "' href='#" . $entry->get_id() . "'>" . ucfirst(strftime("%e/%m/%G", $entry->get_date())) . ' - ' . $entry->get_title() . "</a></h2>\n";
		if ($show_private) {
			$contents = $entry->get_everything();
		} else {
			$contents = $entry->get_public();
		}

		$ignore_nl = FALSE;
		foreach ( $contents as $content ) {
			$content = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\">\\0</a>", $content);
/*foreach ($pictures as $picture) {
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
				echo "\t\t<p class='content'>" . $content . "</p>\n";
			} else {
				echo $content . "\n";
			}
		}
			echo "</div>\n\n";
	}
?>

</body>
</html>
