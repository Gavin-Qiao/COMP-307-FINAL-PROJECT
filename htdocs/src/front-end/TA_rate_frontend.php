<?php


//$var = $_REQUEST["var"];

//if ($var != "")
//{
	session_start();
	$studentusername = $_SESSION["username"];
	$TA_list = Utilities::getTAByStudent($studentusername);
	
	$toReturn = '<div class="login"> <select name="course" id="course-select"> <option value=""> --Please choose an option--</option> '; 
	foreach ($TA_list as $key) 
	{ $toReturn += '<option value="">' + $key + '</option>'  ; }
	
	toReturn += '</select> <br> <button onclick="">Select TA</button> </div>';

	//'<option value=""> Hello there from php </option> '  ;
//}



?>