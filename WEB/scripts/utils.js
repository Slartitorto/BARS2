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

function checkOrari() {
  var from1 = document.getElementById("from1").value;
  var to1 = document.getElementById("to1").value;
  var from2 = document.getElementById("from2").value;
  var to2 = document.getElementById("to2").value;
  var ok = true;
  if (to1 < from1) {
    document.getElementById("from1").style.borderColor = "#E34234";
    document.getElementById("to1").style.borderColor = "#E34234";
    ok = false;
  }
  if (to2 < from2) {
    document.getElementById("from2").style.borderColor = "#E34234";
    document.getElementById("to2").style.borderColor = "#E34234";
    ok = false;
  }
  return ok;
}

$(function() {
  $.datepicker.regional['it'] = {
    closeText: 'Chiudi', // set a close button text
    currentText: 'Oggi', // set today text
    monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',   'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'], // set month names
    monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'], // set short month names
    dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'], // set days names
    dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'], // set short day names
    dayNamesMin: ['Do','Lu','Ma','Me','Gio','Ve','Sa'], // set more short days names
    dateFormat: 'dd/mm/yy' // set format date
  };
  $.datepicker.setDefaults($.datepicker.regional['it']);
  $("#datepicker").datepicker();
});

function checkConfirm() {
  var r = confirm("confermi la cancellazione ?");
  if (r == true) {return true;}
  else {return false;}
}

function preventMultiSubmit() {
  document.getElementById("mybutton").disabled='true';
  document.getElementById("mybutton").style.background='#ff0000';
  return true;
}
