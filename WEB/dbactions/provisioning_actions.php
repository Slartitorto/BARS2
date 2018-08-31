<?php

include "db_connection.php";

function SettaCoockie($ID_UTENTE, $TEMPO)
{ setcookie('LOGIN', $ID_UTENTE, time()+$TEMPO,"/"); }

function PasswordRandom()
{ $PASSWORD = ""; mt_srand(); for($i=0; $i<8; $i++) { $PASSWORD .= "".mt_rand(0, 9).""; } return $PASSWORD; }

function token_gen()
{ $TOKEN = ""; mt_srand(); for($i=0; $i<24; $i++) { $TOKEN .= "".mt_rand(0, 9).""; } return $TOKEN; }


if(@$_POST["act"] == "recuperaPassword") { // ---------------------------------------

  $Sql		=	"SELECT * FROM `utenti` WHERE `email`='".@$_POST["email"]."' AND `stato`='1';";
  $result	=	$conn->query($Sql);
  if(($result->num_rows) == 1)
  {
    $Dati	=	$result->fetch_array();
    $Password	=	PasswordRandom();
    $Token	=	token_gen();
    $Sql	=	"UPDATE `utenti` SET `new_password` = '".md5($Password)."' WHERE `codUtente`='".$Dati["codUtente"]."' LIMIT 1 ;";
    $Query	=	$conn->query($Sql);
    $Sql	=	"UPDATE `utenti` SET `token` = '".$Token."' WHERE `codUtente`='".$Dati["codUtente"]."' LIMIT 1 ;";
    $Query	=	$conn->query($Sql);

    $to = $_POST["email"];
    $subject = "MyHooly - Recupero Password";
    $message	=	"Ciao ".$Dati["email"].",\n
    questa e-mail ti giunge perchè hai richiesto il cambio password.\n\n
    Ti abbiamo predisposto una nuova password; la nuova password è ".$Password." \n
    Dovrai attivare la nuova password tramite questo link:\n
    http://myhooly.hooly.eu/provisioning_actions.php?act=attiva_nuova_pwd&email=" . $to . "&token=" . $Token ." \n\n
    In caso di problemi ti invitiamo a contattarci direttamente all'indirizzo admin@hooly.eu\n
    Ciao !
    ";
    $headers = "From: admin@hooly.eu \r\n" .
    "Reply-To: admin@hooly.eu \r\n";
    mail($to, $subject, $message, $headers);

    header('Location: ../index.php?act=RecuperoPwdMailSent');
  }
  else
  {
    header('Location: ../index.php?act=RecuperoPwdKOUserNotExists');
  }


} else if(@$_POST["act"] == "registrazione") { // ---------------------------------------

  $email	= $_POST["email"];
  $password	= $_POST["password"];
  $sms_tel	= $_POST["sms_tel"];

  $codUtente	= md5($email);
  $codPassword	= md5($password);
  $query	= "SELECT * from `utenti` WHERE `email`='" . $email . "';";
  $result       = $conn->query($query);
  if(($result->num_rows) == 0)
  {

    $query = "SELECT max(t0) + 1 as next_tenant from utenti";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
      $next_tenant = $row["next_tenant"];
    }

    $query = "SELECT * FROM server_settings";
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
      $sendmessage_key=$row["sendmessage_key"];
    }

    $activation_key = rand(100000,999999);

    $query		= "INSERT INTO `utenti` SET `activation_key`='$activation_key', `sms_tel`='$sms_tel', `username`='$email', `codUtente`='$codUtente', `password`='$codPassword', `t0`= $next_tenant, `t1`= 0, `t2`= 0, `t3`= 0, `email`='$email';";
    $result	= $conn->query($query);
    $subject = "PIN per l'attivazione dell'account MyHooly";
    $message = "inserisci nella form il numero: ".$activation_key;
    $website = "http://myhooly.hooly.eu/sendmessage.php?channel=sms&key=".$sendmessage_key."&destination=".$sms_tel."&subject=".$subject."&message=".$message;
    $website = str_replace(" ","%20",$website);
    $content = file_get_contents($website);

    $Messaggio	= "
    Ciao, questa e-mail ti giunge dall'area riservata di ".NOMESITO.".\n\n
    I tuoi dati di accesso sono:\n
    Username: ".$email."\n
    Password: ".$password."\n\n\n
    Questa è la url per confermare l'attivazione del tuo account:\n\n
    ".URLSITO."/dbactions/provisioning_actions.php?act=conferma&cod=".$codUtente."\n\n
    Se invece hai inserito il codice pin inviato via sms, puoi ignorare questo messaggio.\n\n
    In caso di problemi ti invitiamo a contattarci direttamente.
    ";

    mail($email, "Hooly Sensors - Conferma registrazione", $Messaggio, "From: admin@hooly.eu");
    header('Location: ../index.php?act=RegistrazioneOn&codUtente='.$codUtente);

  } else {
    header('Location: ../index.php?act=RegistrazioneKOEmailAlreadyExists');
  }

} else if(@$_GET["act"] == "attiva_nuova_pwd") { // ---------------------------------------


  $token = $_GET["token"];
  $email = $_GET["email"];
  $Sql   = "SELECT * FROM `utenti` WHERE `email`='" . $email . "' AND `token`='" . $token . "';";
  $result = $conn->query($Sql);
  if(($result->num_rows) == 1)
  {
    $Sql          =       "UPDATE `utenti` SET password = new_password WHERE `email`='" . $email . "' AND `token`='" . $token . "';";
    $result               =       $conn->query($Sql);
    $Sql          =       "UPDATE `utenti` SET new_password = '' WHERE `email`='" . $email . "' AND `token`='" . $token . "';";
    $result               =       $conn->query($Sql);
    $Sql          =       "UPDATE `utenti` SET token = '' WHERE `email`='" . $email . "' AND `token`='" . $token . "';";
    $result               =       $conn->query($Sql);
    header('Location: ../index.php?act=RecuperoPwdDone');
  } else {
    header('Location: ../index.php?act=RecuperoPwdTokenKO');
  }


} else if(@$_POST["act"] == "login") { // ---------------------------------------


  $codPassword	=	md5($_POST["password"]);
  $Sql		=	"SELECT * FROM `utenti` WHERE `email`='".@$_POST["email"]."' AND `password`='".$codPassword."' AND `stato`='1';";
  $result		=	$conn->query($Sql);
  if(($result->num_rows) == 1)
  {
    $Dati = $result->fetch_array();
    if(@$_POST["remember"] == 1) $TempoDiValidita = 31536000; else $TempoDiValidita = 72000;
    SettaCoockie($Dati["codUtente"], $TempoDiValidita);
    header('Location: ../status.php');
  }
  else
  {
    header('Location: ../index.php?act=wrongLoginPassword');
  }


} else if(@$_GET["act"]	==	"logout") { // ---------------------------------------


  // da verificare e sistemare
  SettaCoockie(" ", -1);
  header('Location: ../index.php');


} else if(@$_POST["act"]	==	"sms_activation") { // ---------------------------------------


  $codUtente	= $_POST["codUtente"];
  $pin	= $_POST["pin"];
  $query = "SELECT activation_key, stato from utenti where codUtente = '" . $codUtente . "'";
  $result = $conn->query($query);
  if(($result->num_rows) == 1)
  {
    while($row = $result->fetch_assoc()) {
      $registered_pin = $row["activation_key"];
      $stato = $row["stato"];
    }
    if ($stato == 0) {
      if ($pin == $registered_pin) {
        $Sql		=	"UPDATE utenti SET stato = '1' WHERE codUtente = '" . $codUtente . "' LIMIT 1";
        $result		=	$conn->query($Sql);
        header('Location: ../index.php?act=AttivazioneOn');
      } else {
        $Sql		=	"DELETE FROM utenti WHERE stato = '0' AND codUtente = '" . $codUtente . "'";
        $result		=	$conn->query($Sql);
        header('Location: ../index.php?act=Attivazione_sms_KO');
      }
    } else {
      header('Location: ../index.php?act=Attivazione_sms_KO_user_already_active');
    }
  }

} else if(@$_GET["act"]	==	"conferma") { // ---------------------------------------


  // da verificare e sistemare
  $Sql		=	"UPDATE `utenti` SET `stato` = '1' WHERE `codUtente`='".@$_GET["cod"]."' LIMIT 1 ;";
  $result		=	$conn->query($Sql);
  header('Location: ../index.php');


}
?>
