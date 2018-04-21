<?php
include "db_connection.php";
if(isset($_COOKIE['LOGIN']))
{ $COD_UTENTE =	$_COOKIE['LOGIN']; header("Location: status.php"); }
?>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo NOMESITO; ?></title>
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>

  <h2><?php echo NOMESITO; ?></h2>


  <?php if(@$_GET["act"] == "Recovery") {// --------------------------   ?>


    <form class="modal-content animate" method="post" action="azioni.php?act=recuperaPassword">
      <div class="container">
        <h1>Recupero password</h1>
        <label for="uname"><b>Inserisci il tuo indirizzo email</b></label>
        <input type="text" placeholder="email" name="email" required>
        <button type="submit">Invia</button>
        Ti invieremo un messaggio con una nuova password.
        <div>
          <br><br>
          <button type="button" onclick="location.href='index.php';" class="cancelbtn">Annulla</button>
        </div>
      </form>


    <?php } else if(@$_GET["act"] == "RegistrazioneOn") {// --------------------------   ?>


     <div class="modal-content animate">
        <div class="container">
          <h1>Registrazione avvenuta</h1>
          Registrazione effettutata con successo; a breve riceverai una e-mail con un link per attivare la registrazione.<br><br>
          Se non la trovi, prova a controllare nella casella della posta indesiderata, alcuni provider potrebbero scambiarla per spam.<br>
          Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
          <br><br>
          <button type="submit" onclick="location.href='index.php';">Torna alla pagina di login</button>
        </div>
      </div>


    <?php } else if(@$_GET["act"] == "AttivazioneOn") {// --------------------------   ?>


      <div class="modal-content animate">
        <div class="container">
          <h1>Attivazione effettuata</h1>
          La procedura di registrazione si è conclusa con successo, ora puoi effettuare il log-in.<br><br>
          Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
          <br><br>
          <button type="submit" onclick="location.href='index.php';">Torna alla pagina di login</button>
        </div>
      </div>



    <?php } else if(@$_GET["act"] == "RecuperoOn") {// --------------------------  ?>


      <div class="modal-content animate">
        <div class="container">
          <h1>Recupero password</h1>
          Ti è stata inviata una e-mail con nuovi dati di accesso.<br><br>
          Se non la trovi, prova a controllare nella casella della posta indesiderata, alcuni provider potrebbero scambiarla per spam.<br>
          Se hai difficoltà non esitare a <a href=mailto:admin@hooly.eu>contattarci</a>.
          <br><br>
          <button type="submit" onclick="location.href='index.php';">Torna alla pagina di login</button>
        </div>
      </div>


    <?php } else if(@$_GET["act"] == "RecuperoOff") {// --------------------------   ?>


      <div class="modal-content animate">
        <div class="container">
          <h1>Recupero password</h1>
          L'indirizzo email non corrisponde ad un utente attivo
          <br><br>
          <button type="button" onclick="location.href='index.php?act=Recovery';" class="cancelbtn">Torna indietro</button>
        </div>
      </div>


    <?php } else { // ---------- normale login ---------------  ?>


      <form class="modal-content animate" action="azioni.php?act=login">
        <div class="container">
          <h1>Login</h1>
          <label for="uname"><b>Email</b></label>
          <input type="text" placeholder="Inserisci il tuo username" name="uname" required>
          <label for="psw"><b>Password</b></label>
          <input type="password" placeholder="Inserici la tua password" name="psw" required>
          <button type="submit">Login</button>
          <label>
            <input type="checkbox" checked="checked" name="remember" value="1"> Ricordami al prossimo accesso
          </label>
          <div>
            <br><br>
            <button type="button" onclick="location.href='http://www.hooly.eu';" class="cancelbtn">Annulla</button>
            <button type="button" onclick="location.href='registrazione.php';" class="otherbtn">Registrati come nuovo utente</button>
            <span class="psw"><a href="index.php?act=Recovery">Ho dimenticato la password</a></span>
          </div>
        </div>
      </form>

    <?php } ?>

  </body>
  </html>
