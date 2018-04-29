<?php

if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE =    $_COOKIE['LOGIN'];}
else { $COD_UTENTE =    0; header("Location: index.php");}

include "db_connection.php";

if(@$_POST["act"] == "nc_record") { // -----------Regisrazione non conformita


  $nc_date = $_POST["nc_date"];
  $nc_type = $_POST["nc_type"];
  $nc_ac = $_POST["nc_ac"];
  $serial = $_POST["serial"];
  $codUtente = $_POST["cod_utente"];

  $query = "SELECT device_name, position FROM devices WHERE serial = '$serial'";
  $result = $conn->query($query);
  while($row = $result->fetch_assoc()) {
    $device_name = $row["device_name"];
    $position = $row["position"];
  }

  $query = "INSERT INTO `non_conformita` (`nc_date`,`nc_type`,`nc_ac`,`serial`,`codUtente`,`device_name`,`position`) VALUES ('$nc_date','$nc_type','$nc_ac','$serial','$codUtente','$device_name','$position')";
  $result = $conn->query($query);

  header('Location: status.php');


} else if(@$_POST["act"] == "nc_delete") { // ---------------------------------------


  $nc_id = $_POST["nc_id"];
  $mese = $_POST["mese"];
  $anno = $_POST["anno"];
  $query = "DELETE FROM `non_conformita` WHERE nc_id = '$nc_id' ";
  echo $query;
  $result = $conn->query($query);

  header("Location: gestione_nc.php?mese=$mese&anno=$anno");

} else if(@$_POST["act"] == "xxxxxxxx") { // ---------------------------------------


}
?>
