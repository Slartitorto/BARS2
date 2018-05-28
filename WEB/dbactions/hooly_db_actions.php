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

  header('Location: ../status.php');


} else if(@$_POST["act"] == "rm_record") { // ----------- Inserisci Registrazione Manuale


    $serial = $_POST["serial"];
    $codUtente = $_POST["cod_utente"];
    $date = $_POST["date"];
    $item = $_POST["item"];
    $ora = $_POST["ora"];
    $minuto = $_POST["minuto"];
    $temp_gradi = $_POST["temp_gradi"];
    $temp_centesimi = $_POST["temp_centesimi"];

    $query = "SELECT device_name, position FROM devices WHERE serial = '$serial'";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
      $device_name = $row["device_name"];
      $position = $row["position"];
    }

    $splitted_date = preg_split('/\//',$date);
    $giorno = $splitted_date[0];
    $mese = $splitted_date[1];
    $anno = $splitted_date[2];

    $temp = $temp_gradi . "." . $temp_centesimi;

    $query = "DELETE FROM `rilevazioni_manuali` WHERE `codUtente` = '$codUtente' AND `serial` = '$serial' AND `giorno` = '$giorno' AND `mese` = '$mese' AND `anno` = '$anno' AND `item` = '$item'";
    $result = $conn->query($query);

    $query = "INSERT INTO `rilevazioni_manuali` (`codUtente`,`serial`,`device_name`,`position`,`giorno`,`mese`,`anno`,`ora`,`minuto`,`item`,`temp`) VALUES ('$codUtente','$serial','$device_name','$position','$giorno','$mese','$anno','$ora','$minuto','$item','$temp')";
    $result = $conn->query($query);

    header('Location: ../status.php');



} else if(@$_POST["act"] == "nc_modify") { // ----------- Modifica non conformita


  $nc_id = $_POST["nc_id"];
  $nc_date = $_POST["nc_date"];
  $nc_type = $_POST["nc_type"];
  $nc_ac = $_POST["nc_ac"];
  $serial = $_POST["serial"];
  $mese = $_POST["mese"];
  $anno = $_POST["anno"];
  $codUtente = $_POST["cod_utente"];

  $query = "SELECT device_name, position FROM devices WHERE serial = '$serial'";
  $result = $conn->query($query);
  while($row = $result->fetch_assoc()) {
    $device_name = $row["device_name"];
    $position = $row["position"];
  }

  $query = "UPDATE `non_conformita` SET `nc_date` = '$nc_date', `nc_type` = '$nc_type', `nc_ac` = '$nc_ac', `serial` = '$serial', `device_name` = '$device_name' ,`position` = '$position' WHERE `nc_id` = '$nc_id'";
  $result = $conn->query($query);
  header("Location: ../generals.php?act=NC_manage&mese=$mese&anno=$anno");


} else if(@$_POST["act"] == "nc_delete") { // ----------- Cancella non conformità


  $nc_id = $_POST["nc_id"];
  $mese = $_POST["mese"];
  $anno = $_POST["anno"];
  $query = "DELETE FROM `non_conformita` WHERE nc_id = '$nc_id' ";
  $result = $conn->query($query);

  header("Location: ../generals.php?act=NC_manage&mese=$mese&anno=$anno");


} else if(@$_POST["act"] == "rm_delete") { // ----------- Cancella rilevazioni manuali


  $nc_id = $_POST["id"];
  $mese = $_POST["mese"];
  $anno = $_POST["anno"];
  $query = "DELETE FROM `rilevazioni_manuali` WHERE id = '$nc_id' ";
  $result = $conn->query($query);

  header("Location: ../generals.php?act=RM_manage&mese=$mese&anno=$anno");


} else if(@$_POST["act"] == "alarm_pause_record") { // ----------------- Registra pausa allarme

  $alarm_pause_flag_1 = $_POST["alarm_pause_flag_1"];
  $alarm_pause_from_1 = $_POST["alarm_pause_from_1"];
  $alarm_pause_to_1 = $_POST["alarm_pause_to_1"];
  $alarm_pause_flag_2 = $_POST["alarm_pause_flag_2"];
  $alarm_pause_from_2 = $_POST["alarm_pause_from_2"];
  $alarm_pause_to_2 = $_POST["alarm_pause_to_2"];

  $query = "DELETE FROM alarm_pause WHERE codUtente = '$COD_UTENTE'";
  $result = $conn->query($query);
  $query = "INSERT INTO alarm_pause (codUtente, alarm_pause_flag_1,alarm_pause_from_1, alarm_pause_to_1, alarm_pause_flag_2, alarm_pause_from_2, alarm_pause_to_2) VALUES ('$COD_UTENTE','$alarm_pause_flag_1','$alarm_pause_from_1','$alarm_pause_to_1','$alarm_pause_flag_2','$alarm_pause_from_2','$alarm_pause_to_2')";
  $result = $conn->query($query);

  header('Location: ../status.php');


} else if(@$_POST["act"] == "set_personalInfo") { // ------------------- Registra informazioni personali

  $ragione_sociale = $_POST["ragione_sociale"];
  $indirizzo_1 = $_POST["indirizzo_1"];
  $indirizzo_2 = $_POST["indirizzo_2"];
  $cap = $_POST["cap"];
  $citta = $_POST["citta"];
  $telefono = $_POST["telefono"];

  $query = "DELETE FROM personal_info WHERE codUtente = '$COD_UTENTE'";
  $result = $conn->query($query);
  $query = "INSERT INTO personal_info (codUtente, ragione_sociale, indirizzo_1, indirizzo_2, cap, citta, telefono) VALUES ('$COD_UTENTE','$ragione_sociale','$indirizzo_1','$indirizzo_2','$cap','$citta','$telefono')";
  $result = $conn->query($query);

  header('Location: ../status.php');

} else if(@$_POST["act"] == "set_notifyMethod") { // ------------------ Registra metodi di notifica

  if (isset($_POST["telegram_flag"]))  $telegram_flag = $_POST["telegram_flag"]; else $telegram_flag = 0;
  $telegram_chatid = $_POST["telegram_chatid"];
  if (isset($_POST["pushbullett_flag"])) $pushbullett_flag = $_POST["pushbullett_flag"]; else $pushbullett_flag = 0;
  $pushbullett_addr = $_POST["pushbullett_addr"];
  if (isset($_POST["email_flag"])) $email_flag = $_POST["email_flag"]; else $email_flag = 0;
  $email_addr = $_POST["email_addr"];
  if (isset($_POST["whatsapp_flag"])) $whatsapp_flag = $_POST["whatsapp_flag"]; else $whatsapp_flag = 0;
  $whatsapp_tel = $_POST["whatsapp_tel"];

  $query = "DELETE FROM notify_method WHERE codUtente = '$COD_UTENTE'";
  $result = $conn->query($query);

  $query = "INSERT INTO notify_method (codUtente,telegram_flag,telegram_chatid,pushbullett_flag,pushbullett_addr,email_flag,email_addr,whatsapp_flag,whatsapp_tel) VALUES ('$COD_UTENTE','$telegram_flag','$telegram_chatid','$pushbullett_flag','$pushbullett_addr','$email_flag','$email_addr','$whatsapp_flag','$whatsapp_tel')";
  $result = $conn->query($query);

  header('Location: ../status.php');

}
?>
