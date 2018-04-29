<?php

include "db_connection.php";

if(@$_POST["act"] == "nc_record") { // -----------Regisrazione non conformita

  $nc_date = $_POST["nc_date"];
  $nc_type = $_POST["nc_type"];
  $nc_ac = $_POST["nc_ac"];
  $serial = $_POST["serial"];
  $codUtente = $_POST["cod_utente"];

  $Sql          =       "SELECT * FROM `utenti` WHERE `codUtente`='" . $codUtente . "' AND `stato`='1';";
  echo $Sql;
  $result       =       $conn->query($Sql);
  if(($result->num_rows) == 1)
  {
    $Sql        =       "INSERT INTO `non_conformita` (`nc_date`,`nc_type`,`nc_ac`,`serial`,`codUtente`) VALUES ('$nc_date','$nc_type','$nc_ac','$serial','$codUtente')";
    echo $Sql;
    $Query      =       $conn->query($Sql);

    header('Location: status.php');
  }
  else
  {
    echo "Fail !";
    //    header('Location: index.php?act=RecuperoPwdKOUserNotExists');
  }


} else if(@$_GET["act"] == "xxxxxx") { // ---------------------------------------
}
?>
