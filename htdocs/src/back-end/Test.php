<?php

/* Database test
$pdo = new PDO('sqlite:toy.db');

$statement = $pdo -> query("SELECT * FROM Test;");

$rows = $statement -> fetchAll(PDO::FETCH_ASSOC);

var_dump($rows);
*/

// Autoloader
spl_autoload_register(function ($class_name)
{
    require $class_name.'.php';
});

/* CSV downloadable test
$fileContent  = "Id,First name,Last name\n";
$fileContent .= "1,Jack,Doe\n";
$fileContent .= "2,Jill,Jackson\n";

header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=output.csv");
header("Pragma: no-cache");
header("Expires: 0");

echo $fileContent;
*/

$stm_lib = new SQL_STATEMENTS();

$login  = new UserManagement($stm_lib::DATABASE_NAME);
$course = new CourseManagement($stm_lib::DATABASE_NAME);


#$login->errCode_decoder($login->Register_User("admin2", "sysopAdmin1", "260927121", "Mohan", "Qiao","2@3"));
#$login->errCode_decoder($login->Register_User("admin3", "sysopAdmin1", "260927122", "Mohan", "Qiao","2@4"));
#$login->errCode_decoder($login->Register_User("admin4", "sysopAdmin1", "260927123", "Mohan", "Qiao","2@5"));

print_r(get_class_methods(CourseManagement::class));
print_r(get_class_methods(UserManagement::class));

