<?php
include "dbactions/db_connection.php";
if(isset($_COOKIE['LOGIN']))
{ $COD_UTENTE =	$_COOKIE['LOGIN']; header("Location: status.php"); }
?>

<head>
  <title><?php echo NOMESITO; ?></title>

  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/stile.css" rel="stylesheet" type="text/css" />

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <script src="scripts/utils.js"></script>
</head>

<body>

  <h1><?php echo NOMESITO; ?></h1>

  <?php if(@$_GET["act"] == "Recovery") {// --------------------------   ?>


    <div class="modal-content animate">
      <form method="post" action="dbactions/provisioning_actions.php">
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

    <?php
    $codUtente	= $_GET["codUtente"];
    ?>
    <div class="modal-content animate">
      <h2>Registrazione avvenuta con successo !</h2>
      <br><br>
      <center>
        Ti abbiamo appena inviato una mail ed un pin via sms.<br><br>
        Puoi seguire il link indicato nella mail <br><b>oppure</b><br>
        inserire il pin di 6 cifre nello spazio qui sotto.<br><br><br>
        Questa operazione è necessaria per attivare l'account.<br>
        <br><br>
        <form action="dbactions/provisioning_actions.php" method="post">
          <input type="text" class="slim" name="pin" pattern="[0-9]{6}" size="6" maxlength="6" required>
          <input type="hidden" name="act" value="sms_activation">
          <input type="hidden" name="codUtente" value="<?php echo $codUtente ?>">
        </center>
        <button type="submit" class="greenbtn centeredbtn">Attiva l'account</button>
      </form>
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


  <?php } else if(@$_GET["act"] == "Attivazione_sms_KO_user_already_active") {// --------------------------   ?>


    <div class="modal-content animate">
      <h2>Attivazione sms fallita</h2>
      <br><br>
      C'e' stato un problema nella procedura di attivazione SMS:<br>
      forse hai già attivato l'utente tramite email.<br>
      Prova ad effettuare il login.
      <br><br>
      Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
      <button type="button" onclick="location.href='index.php';" class="greenbtn centeredbtn">Login</button>
    </div>


  <?php } else if(@$_GET["act"] == "Attivazione_sms_KO") {// --------------------------   ?>


    <div class="modal-content animate">
      <h2>Attivazione fallita</h2>
      <br><br>

      C'e' stato un problema nella procedura di attivazione<br>
      forse hai utilizzato un pin errato.
      <br><br>
      Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
      <button type="button" onclick="location.href='index.php?act=Registrazione';" class="redbtn centeredbtn">Riprova a registrarti</button>
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


  <?php } else if(@$_GET["act"] == "RegistrazioneKOEmailAlreadyExists") {// --------------------------   ?>


    <div class="modal-content animate">
      <h2>Errore di registrazione</h2>
      <br><br>
      L'indirizzo email risulta già presente.
      <br><br>
      Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
      <button type="button" onclick="location.href='index.php?act=Registrazione';" class="redbtn centeredbtn">Torna indietro</button>
    </div>


  <?php } else if(@$_GET["act"] == "Registrazione") { // ---------- Registrazione -  ?>


    <div class="modal-content animate">
      <form action="dbactions/provisioning_actions.php" onsubmit="return checkPassword()" method="post">
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
        <input type="password" style="width:100%" placeholder="Enter Password" name="password" id="password" pattern=".{5,12}" title="La password deve contenere un minimo di 5 ed un massimo di 12 caratteri" required>
        <br> <br>
        <b>Ripeti la password:</b>
        <br>
        <input type="password" style="width:100%" placeholder="Repeat Password" name="psw-repeat" id="confirm_password" required>

        <br> <br>
        <b>Cellulare</b> (il pin di attivazione verrà inviato per sms)
        <br>
        <b>+39</b><input type="text" placeholder="" pattern="[0-9]{8,11}" maxlength="11" size="11" name="sms_tel" required>


        <input name="act" type="hidden" value="registrazione">
        <button type="submit" class="greenbtn centeredbtn">Registrati</button>
        <br>
        <button type="button" onclick="location.href='index.php';" class="redbtn">Annulla</button>
      </form>
    </div>


  <?php } else { // ---------- normale login ---------------  ?>


    <div class="modal-content animate">
      <form action="dbactions/provisioning_actions.php" method="post">
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
