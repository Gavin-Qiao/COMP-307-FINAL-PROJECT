<?php

// This function displays all the lines from some file at path $path.
finction display($path)
{
	$file = fopen($path,"r");
	while(!feof($file))
	{
		$line = fgets($file);
		echo $line;
	}
	fline($file);
}

if (sizeof($_GET)==0) ; //print student

for ($i = 0;$i<sizeof($_GET);$i++)
{

	if (sizeof($_GET[strval($i)]=="student")
	{
		
	}
	else if ($_GET[strval($i)]=="teacher")
	{
		
	}
	else if ($_GET[strval($i)]=="TA")
	{

	}
	else if ($_GET[strval($i)=="system_operator")
	{

	}
	else if ($_GET[strval($i)]=="TA_admin")
	{

	}
}

// DISPLAY FOOTER

?>


