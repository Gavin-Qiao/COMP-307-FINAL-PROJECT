<?php

require_once 'utilities.php';
require_once '../../matter/dbf/CSV_Utilities.php';
require_once '../../matter/dbf/UserManagement.php';

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $userManager = new UserManagement('../../matter/dbf/TA_Management_Website.db');

    $user_id = Utilities::cleanInput($_POST["user_id"]);

    $info[] = $userManager->Get_TA_Info($user_id);

    CSV_Utilities::Downloadable_CSV($info, "TA_information.csv");
}