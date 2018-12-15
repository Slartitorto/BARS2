<?php
if(isset($_COOKIE['LOGIN'])) { $COD_UTENTE = $_COOKIE['LOGIN'];
} else { $COD_UTENTE = 0; header("Location: index.php"); }
include "dbactions/db_connection.php";
?>

<head>
  <title>Hooly settings</title>
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  <link href="css/reset.css" rel="stylesheet" type="text/css" />
  <link href="css/stile.css" rel="stylesheet" type="text/css" />

  <link rel="apple-touch-icon" href="/icone/app_icon128.png">
  <link rel="icon" href="/icone/temp_icon.png">

  <script src="scripts/jquery.min.js"></script>
  <script src="scripts/checkMatchPasswords.js"></script>

</head>

<body>
  <BR>
      <?php include 'includes/headerTableMenu.php';

      if(isset($_POST['serial']))
      // START self_reload
      {
        $serial=$_POST['serial'];
        $device_name=$_POST['device_name'];
        $position=$_POST['position'];
        $code_period=$_POST['code_period'];
        $min_ok=$_POST['min_ok'];
        $max_ok=$_POST['max_ok'];

        if(!isset($_POST['armed']))
        { $armed=0; }
        else
        { $armed=1; }

        if ((preg_match("/^[a-zA-Z0-9_ ]+$/", $device_name)) and (preg_match("/^[a-zA-Z0-9_ ]+$/", $position)) and (preg_match("/^-?[0-9]{1,3}+$/", $min_ok)) and (preg_match("/^-?[0-9]{1,3}+$/", $max_ok)))
        {
          $sql = "UPDATE devices set device_name='$device_name', position='$position', code_period='$code_period', min_ok='$min_ok', max_ok='$max_ok', armed='$armed' where serial='$serial'";
          $result = $conn->query($sql);
        }

        $serial="";
        $device_name="";
        $position="";
        $min_ok="";
        $max_ok="";
        $armed="";
        $code_period="";
      }
      // END self_reload

      $query = "SELECT idUtente,t0,t1,t2,t3 FROM utenti WHERE codUtente='$COD_UTENTE'";
      $result = $conn->query($query);
      while($row = $result->fetch_assoc()) {
        $idUtente = $row["idUtente"];
        $tenant0 = $row["t0"];
        $tenant1 = $row["t1"];
        $tenant2 = $row["t2"];
        $tenant3 = $row["t3"];
      }

      $sql = "SELECT serial, device_name, position, min_ok, max_ok, armed, code_period FROM devices where tenant in ($tenant0,$tenant1,$tenant2,$tenant3) order by serial";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        $x=0;
        while($row = $result->fetch_assoc()) {
          $serial[$x] = $row["serial"];
          $device_name[$x] = $row["device_name"];
          $position[$x] = $row["position"];
          $min_ok[$x] = $row["min_ok"];
          $max_ok[$x] = $row["max_ok"];
          $armed[$x] = $row["armed"];
          $code_period[$x] = $row["code_period"];
          ++$x;
        }

        echo "<div class=\"modal-content device_settings\">";
        echo "<br><br>";
        echo "<table class=\"centered device_settings\">\n";
        echo "<tr><th>Seriale</th><th>Nome hooly</th><th>Posizione</th><th>Rilevamento</th>";
        echo "<th>Min</th><th>Max</th><th>Notifica</th></tr>\n";
        echo "<tr><td><br><br></td></tr>";

        for($x=0;$x<$result->num_rows;$x++) {
          echo "<form action =\"" . $_SERVER['PHP_SELF'] . "\" method=\"POST\">";
          echo "<TR>";
          echo "<TD>" . $serial[$x] . "</TD>\n";
          echo "<input type=\"hidden\" name=\"serial\" value=\"" . $serial[$x] . "\">\n";
          echo "<TD><input type=\"text\" class=\"slim\" name=\"device_name\" value=\"" . $device_name[$x] . "\" size=15 onchange=\"this.form.submit()\"></TD>\n";
          echo "<TD><input type=\"text\" class=\"slim\" name=\"position\" value=\"" . $position[$x] . "\" size=15 onchange=\"this.form.submit()\"></TD>\n";
echo "<input type=\"hidden\" name=\"code_period\" value=\"5\">\n";
          echo "<TD><select disabled class=\"slim\" name=\"code_period\" onchange=\"this.form.submit()\">\n";

          echo "<option value= \"1\"";
          if ($code_period[$x] == 1) { echo " selected";}
          echo "> 5 sec. </option>\n";

          echo "<option value= \"2\"";
          if ($code_period[$x] == 2) { echo " selected";}
          echo "> 15 sec. </option>\n";

          echo "<option value= \"3\"";
          if ($code_period[$x] == 3) { echo " selected";}
          echo "> 30 sec. </option>\n";

          echo "<option value= \"4\"";
          if ($code_period[$x] == 4) { echo " selected";}
          echo "> 1 min. </option>\n";

          echo "<option value= \"5\"";
          if ($code_period[$x] == 5) { echo " selected";}
          echo "> 3 min. </option>\n";

          echo "<option value= \"6\"";
          if ($code_period[$x] == 6) { echo " selected";}
          echo "> 5 min. </option>\n";

          echo "<option value= \"7\"";
          if ($code_period[$x] == 7) { echo " selected";}
          echo "> 15 min. </option>\n";

          echo "<option value= \"8\"";
          if ($code_period[$x] == 8) { echo " selected";}
          echo "> 30 min. </option>\n";

          echo "<option value= \"9\"";
          if ($code_period[$x] == 9) { echo " selected";}
          echo "> 60 min. </option>\n";
          echo "</select>";
          echo "<TD><select class=\"slim\" name=\"min_ok\" onchange=\"this.form.submit()\">\n";
          for ($i = -40; $i <= $max_ok[$x] -1; $i++) {
            echo "<option value= \"$i\"";
            if ($min_ok[$x] == $i) { echo " selected";}
            echo ">$i °C</option>\n";

          }
          echo "</select>";

          echo "<TD><select class=\"slim\" name=\"max_ok\" onchange=\"this.form.submit()\">\n";
          for ($i = $min_ok[$x] +1; $i <= 80; $i++) {
            echo "<option value= \"$i\"";
            if ($max_ok[$x] == $i) { echo " selected";}
            echo ">$i °C</option>\n";
          }
          echo "</select>";

          if ($armed[$x] == 1)
          {
            echo "<TD><input name=\"armed\" type=checkbox value=\"1\" checked=\"checked\" onchange=\"this.form.submit()\"></TD>\n";
          }
          else
          {
            echo "<TD><input name=\"armed\" type=checkbox value=\"1\" onchange=\"this.form.submit()\"></TD>\n";
          }

          echo "</TR>\n";
          echo "</form>\n";
        }
        echo "</table>\n";

      } else {
        echo "0 results";
      }

      $conn->close();
      ?>
      <br><br>
    </div>
  </body>
