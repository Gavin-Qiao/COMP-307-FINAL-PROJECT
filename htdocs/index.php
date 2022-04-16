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

//if (sizeof($_GET)==0)
//{
	//display("MATTER/CONTENT/top-of-all-files.txt");
	//display("MATTER/CONTENT/login-style.txt");
	//display("MATTER/CONTENT/top-of-all-bodys.txt");
	//display("MATTER/CONTENT/header.txt");
	//display("MATTER/CONTENT/login-body.txt");
	//display("MATTER/CONTENT/bottom-of-all-files.txt");
//}

display("src/front-end/index.html");
//display("index.html");

?>
