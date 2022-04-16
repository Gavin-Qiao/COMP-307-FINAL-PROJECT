<?php

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

if (sizeof($_GET)==0)
{
	display("HTDOCS/MATTER/CONTENTS/top-of-all-files.txt");
	display("HTDOCS/MATTER/CONTENTS/login-style.txt");
	display("HTDOCS/MATTER/CONTENTS/top-of-all-bodys.txt");
	display("HTDOCS/MATTER/CONTENTS/header.txt");
	display("HTDOCS/MATTER/CONTENTS/login-body.txt");
	display("HTDOCS/MATTER/CONTENTS/bottom-of-all-files.txt");
}

?>
