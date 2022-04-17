<?php

session_start();

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

display("../../matter/content/dashboard/dashboard_top.txt");

if ($_SESSION["student"]=="true")
{
    display("../../matter/content/dashboard/dashboard_ta_rate.txt");
}
else if ($_SESSION["ta"]=="true" || $_SESSION["instructor"]=="true")
{
	display("../../matter/content/dashboard/dashboard_ta_manage.txt");
}
else if ($_SESSION["sysop"]=="true")
{
	display("../../matter/content/dashboard/dashboard_sysop.txt");
}
else if ($_SESSION["admin"]=="true")
{
	display("../../matter/content/dashboard/dashboard_ta_admin.txt");
}


// DISPLAY FOOTER
display("../../matter/content/dashboard/dashboard_end.txt");

?>


