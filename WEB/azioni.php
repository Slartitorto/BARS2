<?php

include "db_connection.php";

function SettaCoockie($ID_UTENTE, $TEMPO)
{ setcookie('LOGIN', $ID_UTENTE, time()+$TEMPO); }

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
    http://myhooly.hooly.eu/azioni.php?act=attiva_nuova_pwd&email=" . $to . "&token=" . $Token ." \n\n
    In caso di problemi ti invitiamo a contattarci direttamente all'indirizzo admin@hooly.eu\n
    Ciao !
    ";
    $headers = "From: admin@hooly.eu \r\n" .
    "Reply-To: admin@hooly.eu \r\n";
    mail($to, $subject, $message, $headers);

    header('Location: index.php?act=RecuperoPwdMailSent');
  }
  else
  {
    header('Location: index.php?act=RecuperoPwdKOUserNotExists');
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
    header('Location: index.php?act=RecuperoPwdDone');
  } else {
    header('Location: index.php?act=RecuperoPwdTokenKO');
  }


} else if(@$_POST["act"] == "login") { // ---------------------------------------


  $codPassword	=	md5($_POST["password"]);
  $Sql		=	"SELECT * FROM `utenti` WHERE `email`='".@$_POST["email"]."' AND `password`='".$codPassword."' AND `stato`='1';";
  $result		=	$conn->query($Sql);
  if(($result->num_rows) == 1)
  {
    $Dati		=	$result->fetch_array();
    if(@$_POST["ricorda"]	==	1)
    $TempoDiValidita	=	31536000;
    else
    $TempoDiValidita	=	72000;
    SettaCoockie($Dati["codUtente"], $TempoDiValidita);
    header('Location: status.php');
  }
  else
  {
    header('Location: index.php?act=wrongLoginPassword');
  }


} else if(@$_GET["act"]	==	"logout") { // ---------------------------------------


  // da verificare e sistemare
  SettaCoockie(" ", -1);
  header('Location: index.php');


} else if(@$_GET["act"]	==	"conferma") { // ---------------------------------------


  // da verificare e sistemare
  $Sql		=	"UPDATE `utenti` SET `stato` = '1' WHERE `codUtente`='".@$_GET["cod"]."' LIMIT 1 ;";
  $result		=	$conn->query($Sql);
  header('Location: index.php');


}
?>
