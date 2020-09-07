<?php
$user = "admin_runead";
$password = "IKRd3sZ7dK";
$host = "localhost";
$dbase = "admin_runead";
$table = "mail_subscribe";

$email= $_POST['email'];

$dbc= mysqli_connect($host,$user,$password, $dbase)
or die("Unable to select database - If you see this the database is currently having issues! Should be back online shortly.");


$query= "INSERT INTO $table  ". "VALUES ('$email')";

mysqli_query ($dbc, $query)
or die ("Error querying database - If you see this the database is currently having issues! Should be back online shortly.");

echo 'You have been successfully added to the RuneAd Newsletter!' . '<br>';

mysqli_close($dbc);

?>
