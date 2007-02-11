<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">

<?php
	require_once( "txt_diary.php" );
	$diary = new txt_diary();
?>


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta name="author" content="<?php echo $diary->author; ?>" />
	<title><?php echo $diary->name; ?></title>
	<link href="diary.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php
	echo "<h1 class=\"name\">" . $diary->name . "</h1>\n\n";
	echo "<p class=\"description\">" . $diary->description . "</p>\n\n\n";
?>

<div align="right">
<form action="<?php echo $_SERVER[ "PHP_SELF" ] ?>" method="post" title="Enter the password to access the private data">
	<input type="password" name="password" />
	<input value="Go!" name="go_private" type="submit" />
</form>
</div>

<?php

	/**
	* Check if a specified data has been request or it's to show everything.
	*/
	if ( ! isset( $_REQUEST[ "entry" ] ) ) {
		$entries = $diary->get_all_entries();
	} else {
		$entries = array();
		$key = strtotime( $_REQUEST[ "entry" ] );
		$entries[] = $diary->get_entry( $key );
		if ( is_null( $entries[ 0 ] ) ) {
			unset( $entries[ 0 ] );
		}
	}

	/**
	* Check if private data must be shown (check the password).
	*/
	if ( isset( $_REQUEST[ "password" ] ) ) {
		if ( $_REQUEST[ "password" ] == $diary->password ) {
			$diary->enable_private = 1;
		}
	}


	/**
	* Get asked entries.
	*/
	if ( count( $entries ) > 0 ) {
		arsort( $entries );
		foreach ( $entries as $entry ) {
			echo "<div class=\"entry\" id=\"entry$entry->date\">\n";
			echo "\t<h2><a name=\"" . $entry->get_id() . "\" href=\"#" . $entry->get_id() . "\">" . $entry->get_date() . " - " . $entry->title . "</a></h2>\n";
			if ( $diary->enable_private ) {
				$contents = $entry->get_everything();
			} else {
				$contents = $entry->get_public();
			}

			$pictures = $entry->get_pictures();
			$objects = $entry->get_objects();
			$ignore_nl = FALSE;
			foreach ( $contents as $content ) {
				$content = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\">\\0</a>", $content);
				foreach ( $pictures as $picture ) {
					$content = ereg_replace( basename( $picture ), "<div class=\"img\" align=\"center\"><img src=\"" . $picture . "\" /></div>", $content);
				}
				foreach ( $objects as $object ) {
					$content = ereg_replace( basename( $object ), "<a href=\"" . $object . "\">" . basename( $object ) . "</a>", $content );
				}

				if (stristr(trim($content),'<pre>') != FALSE) {
					$ignore_nl = TRUE;
				}
				if (stristr(trim($content),'</pre>') != FALSE) {
					$ignore_nl = FALSE;
				}
				if (!$ignore_nl) {
					echo "\t\t<p class=\"content\">" . $content . "</p>\n";
				} else {
					echo $content . "\n";
				}
			}

			echo "</div>\n\n";
		}
	}
	?>

</body>
</html>
