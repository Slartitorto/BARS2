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
