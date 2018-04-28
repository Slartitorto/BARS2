<?php
include "db_connection.php";

if(@$_POST["invio"])
{
  $email	= $_POST["email"];
  $password	= $_POST["password"];
  $codUtente	= md5($email);
  $codPassword	= md5($password);
  $Sql		= "SELECT * from `utenti` WHERE `email`='" . $email . "';";
  $result       = $conn->query($Sql);
  if(($result->num_rows) == 0)
  {

    $Sql		= "INSERT INTO `utenti` SET `username`='".@$_POST["email"]."', `codUtente`='".$codUtente."', `password`='".$codPassword."', `t0`= 1, `t1`= 0, `t2`= 0, `t3`= 0, `email`='".@$_POST["email"]."';";
    $result	= $conn->query($Sql);
    $Messaggio	= "
    Ciao, questa e-mail ti giunge dall'area riservata di ".NOMESITO.".\n\n
    I tuoi dati di accesso sono:\n
    Username: ".$_POST["email"]."\n
    Password: ".$_POST["password"]."\n\n\n
    Questa è la url per confermare l'attivazione del tuo account:\n\n
    ".URLSITO."/provisioning_actions.php?act=conferma&cod=".$codUtente."\n\n
    In caso di problemi ti invitiamo a contattarci direttamente.
    ";

    mail($_POST["email"], "Home Sensors - Conferma registrazione", $Messaggio, "From: admin@hooly.eu");
    header('Location: index.php?act=RegistrazioneOn');

  } else {
    header('Location: index.php?act=RegistrazioneOffEmailAlreadyExists');
  }

}
?>
<!DOCTYPE html>
<html>
<head>

  <link href="style.css" rel="stylesheet" type="text/css">

  <script>
  function checkPassword() {
    var pass1 = document.getElementById("password").value;
    var pass2 = document.getElementById("confirm_password").value;
    var ok = true;
    if (pass1 != pass2) {
      document.getElementById("password").style.borderColor = "#E34234";
      document.getElementById("confirm_password").style.borderColor = "#E34234";
      ok = false;
    }
    return ok;
  }
</script>
</head>

<body>
  <h2><?php echo NOMESITO; ?></h2>
  <form class="modal-content animate" action="registrazione.php" onsubmit="return checkPassword()" method="post">
    <div class="container">
      <h1>Registrazione</h1>
      <p>Inserisci i tuoi dati per creare il nuovo account:</p>

      <label for="email"><b>Email</b></label>
      <input type="email" placeholder="Enter Email" name="email" required>

      <label for="psw"><b>Password</b></label>
      <input type="password" placeholder="Enter Password" name="password" id="password" pattern="[A-Za-z0-9]{6,8}" title="La passowrd può contenere lettere e numeri, un minimo di 6 ed un massimo di 8 caratteri" required>

      <label for="psw-repeat"><b>Repeat Password</b></label>
      <input type="password" placeholder="Repeat Password" name="psw-repeat" id="confirm_password" required>

      <div>
        <br><br>
        <input name="invio" type="hidden" value="1">
        <button type="button" onclick="location.href='index.php';" class="cancelbtn">Annulla</button>
        <button type="submit" class="otherbtn">Registrati</button>
      </div>
    </div>
  </form>

</body>
</html>
