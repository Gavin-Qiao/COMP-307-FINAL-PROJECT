<?php

// This function displays all the lines from some file at path $path.
function display($path)
{
	$file = fopen($path,"r");
	while(!feof($file))
	{
		$line = fgets($file);
		echo $line;
	}
	fclose($file);
}

// foreach ($_GET as $key => $value) { }

session_start();
$_SESSION["course"] = $_POST["course"];

display("../../matter/content/ta_manage/ta_manage_top.txt");


if ( $_SESSION["instructor"]=="true")
{
	display("../../matter/content/ta_manage/instructor_display.txt");
}

// DISPLAY FOOTER
display("../../matter/content/ta_manage/ta_manage_end.txt");

?>