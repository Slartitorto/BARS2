<?php
include "db_connection.php";
if(isset($_COOKIE['LOGIN']))
{ $COD_UTENTE =	$_COOKIE['LOGIN']; header("Location: status.php"); }
?>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo NOMESITO; ?></title>
  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/stile.css" rel="stylesheet" type="text/css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />


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

  <h1><?php echo NOMESITO; ?></h1>

  <?php if(@$_GET["act"] == "Recovery") {// --------------------------   ?>


    <div class="modal-content animate">
      <form method="post" action="provisioning_actions.php">
        <h2>Recupero password</h2>
        <br> <br>
        <b>Inserisci il tuo indirizzo email</b>
        <br><br>
        Ti invieremo un messaggio con una nuova password.
        <br><br>
        <input type="email" style="width:100%" placeholder="email" name="email" required>
        <input type="hidden" name="act" value="recuperaPassword">
        <button type="submit" class="greenbtn centeredbtn">Invia</button>
        <button type="button" onclick="location.href='index.php';" class="redbtn">Annulla</button>
      </form>
    </div>


  <?php } else if(@$_GET["act"] == "RegistrazioneOn") {// --------------------------   ?>


    <div class="modal-content animate">
      <h2>Registrazione avvenuta</h2>
      <br> <br>
      Registrazione effettutata con successo; a breve riceverai una e-mail con un link per attivare la registrazione.
      <br><br>
      Se non la trovi, prova a controllare nella casella della posta indesiderata, alcuni provider potrebbero scambiarla per spam.
      <br><br>
      Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
      <button type="submit" class="greenbtn centeredbtn" onclick="location.href='index.php';">Torna alla pagina di login</button>
    </div>


  <?php } else if(@$_GET["act"] == "AttivazioneOn") {// --------------------------   ?>


    <div class="modal-content animate">
      <h2>Attivazione effettuata</h2>
      <br><br>
      La procedura di registrazione si è conclusa con successo, ora puoi effettuare il log-in.
      <br><br>
      Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
      <button type="submit" class="greenbtn centeredbtn" onclick="location.href='index.php';">Torna alla pagina di login</button>
    </div>


  <?php } else if(@$_GET["act"] == "RecuperoPwdMailSent") {// --------------------------  ?>


    <div class="modal-content animate">
      <h2>Recupero password</h2>
      <br><br>
      Ti è stata inviata una e-mail con nuovi dati di accesso.<br>
      Se non la trovi, prova a controllare nella casella della posta indesiderata, alcuni provider potrebbero scambiarla per spam.<br>
      <br><br>
      Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
      <button type="submit" class="greenbtn centeredbtn" onclick="location.href='index.php';">Torna alla pagina di login</button>
    </div>


  <?php } else if(@$_GET["act"] == "RecuperoPwdKOUserNotExists") {// --------------------------   ?>


    <div class="modal-content animate">
      <h2>Recupero password</h2>
      <br><br>
      L'indirizzo email non corrisponde ad un utente attivo.
      <br><br>
      Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
      <button type="button" onclick="location.href='index.php?act=Recovery';" class="greenbtn centeredbtn">Torna indietro</button>
    </div>


  <?php } else if(@$_GET["act"] == "RecuperoPwdTokenKO") {// --------------------------   ?>


    <div class="modal-content animate">
      <h2>Recupero password</h2>
      <br><br>

      C'e' stato un problema nella procedura di reset password, forse hai utilizzato un link errato o obsoleto.
      <br><br>
      Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
      <button type="button" onclick="location.href='index.php?act=Recovery';" class="redbtn centeredbtn">Torna indietro</button>
    </div>


  <?php } else if(@$_GET["act"] == "RecuperoPwdDone") {// --------------------------   ?>


    <div class="modal-content animate">
      <h2>Recupero password</h2>
      <br><br>
      La tua password è stata reimpostata correttamente
      <button type="button" onclick="location.href='index.php';" class="greenbtn centeredbtn">Torna alla pagina di login</button>
    </div>


  <?php } else if(@$_GET["act"] == "wrongLoginPassword") {// --------------------------   ?>


    <div class="modal-content animate">
      <h2>Errore di credenziali</h2>
      <br><br>
      L'indirizzo email e la password non corrispondono a nessun utente attivo.<br>
      <br><br>
      Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
      <button type="button" onclick="location.href='index.php';" class="redbtn centeredbtn">Torna indietro</button>
    </div>


  <?php } else if(@$_GET["act"] == "RegistrazioneOffEmailAlreadyExists") {// --------------------------   ?>


    <div class="modal-content animate">
      <h2>Errore di registrazione</h2>
      <br><br>
      L'indirizzo email risulta già presente.
      <br><br>
      Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
      <button type="button" onclick="location.href='registrazione.php';" class="redbtn centeredbtn">Torna indietro</button>
    </div>


  <?php } else if(@$_GET["act"] == "Registrazione") { // ---------- Registrazione -  ?>


    <div class="modal-content animate">
      <form action="provisioning_actions.php" onsubmit="return checkPassword()" method="get">
        <h2>Registrazione</h2>
        <br> <br>
        Inserisci i tuoi dati per creare il nuovo account:
        <br> <br>
        <b>Email</b>
        <br>
        <input type="email" style="width:100%" placeholder="Enter Email" name="email" required>
        <br> <br>
        <b>Password:</b>
        <br>
        <input type="password" style="width:100%" placeholder="Enter Password" name="password" id="password" pattern="[A-Za-z0-9]{5,12}" title="La passowrd può contenere lettere e numeri, un minimo di 5 ed un massimo di 12 caratteri alfanumerici" required>
        <br> <br>
        <b>Ripeti la password:</b>
        <br>
        <input type="password" style="width:100%" placeholder="Repeat Password" name="psw-repeat" id="confirm_password" required>
        <input name="act" type="hidden" value="registrazione">
        <button type="submit" class="greenbtn centeredbtn">Registrati</button>
        <br>
        <button type="button" onclick="location.href='index.php';" class="redbtn">Annulla</button>
      </form>
    </div>


  <?php } else { // ---------- normale login ---------------  ?>


    <div class="modal-content animate">
      <form action="provisioning_actions.php" method="post">
        <h2>Login</h2>
        <br> <br> <br>
        <b>Email</b>
        <br>
        <input type="email" style="width:100%" placeholder="Inserisci la tua email" name="email" required>
        <br> <br>
        <b>Password</b>
        <br>
        <input type="password" style="width:100%" placeholder="Inserici la password" name="password" required>
        <button type="submit" class="greenbtn centeredbtn" >Login</button>
        <center><a href="index.php?act=Recovery">Ho dimenticato la password</a></center>
        <br><br><br>
        <input type="hidden" name="act" value="login">
        <input type="checkbox" checked="checked" name="remember" value="1"> Ricordami al prossimo accesso
        <br>
        <button type="button" onclick="location.href='http://www.hooly.eu';" class="redbtn">Annulla</button>
        <button type="button" onclick="location.href='index.php?act=Registrazione';" class="greenbtn">Registrati come nuovo utente</button>
      </form>
    </div>

  <?php } ?>

</body>
</html>
