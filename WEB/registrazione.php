<?php
include "db_connection.php";

if(@$_POST["invio"])
{
  $codUtente	=	md5($_POST["email"]);
  $codPassword	=	md5($_POST["password"]);
  $Sql		=	"INSERT INTO `utenti` SET `username`='".@$_POST["email"]."', `codUtente`='".$codUtente."', `password`='".$codPassword."', `email`='".@$_POST["email"]."';";
  $result		=	$conn->query($Sql);
  $Messaggio	=	"
  Ciao, questa e-mail ti giunge dall'area riservata di ".NOMESITO.".\n\n
  I tuoi dati di accesso sono:\n
  Username: ".$_POST["email"]."\n
  Password: ".$_POST["password"]."\n\n\n
  Questa è la url per confermare l'attivazione del tuo account:\n\n
  ".URLSITO."/azioni.php?act=conferma&cod=".$codUtente."\n\n
  In caso di problemi ti invitiamo a contattarci direttamente.
  ";

  //	mail($_POST["email"], "Home Sensors - Conferma registrazione", $Messaggio, "From: registrazione@slatitorto.eu");
  //	header('Location: index.php?act=RegistrazioneOn');
}
?>
<head>
  <!DOCTYPE html>
  <html>
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
      <input type="text" placeholder="Enter Email" name="email" required>

      <label for="psw"><b>Password</b></label>
      <input type="password" placeholder="Enter Password" name="password" id="password" required>

      <label for="psw-repeat"><b>Repeat Password</b></label>
      <input type="password" placeholder="Repeat Password" name="psw-repeat" id="confirm_password" required>

      <p>La creazione dell'account comporta l'accettazione dei nostri <a href="#" style="color:dodgerblue">Termini e condizioni</a>.</p>

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
