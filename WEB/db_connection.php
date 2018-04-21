<?php

// DATI DI CONNESSIONE AL DATABASE
$servername = "localhost";
$username = "hooly";
$password = "hooly_pwd";
$dbname = "hooly";


//      ACCESSO AL DATABASE
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        exit();
}

define("NOMESITO", "MyHooly");
define("URLSITO", "http://myhooly.hooly.eu");
?>
