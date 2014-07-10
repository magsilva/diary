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
	if (isset($_REQUEST['password']) && ! empty($_REQUEST['password'])) {
		$diary->is_password_correct($_REQUEST['password']);
	}

        if (isset($_REQUEST['slice'])) {
                $slice = $_REQUEST['slice'];
                $data = $diary->get_data($slice);
        } else {
                $data = $diary->get_data();
        }
	echo $data;
?>

</body>
</html>
