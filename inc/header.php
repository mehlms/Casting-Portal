<?php include "db.php";

if (!$MYACCOUNT && $_SERVER['REQUEST_URI'] != "/login/") header("Location: /login/"); // IF USER IS NOT LOGGED IN -> REDIRECT TO /LOGIN/
else if ($MYACCOUNT && $MYACCOUNT['role'] == null && $_SERVER['REQUEST_URI'] != "/complete/") header("Location: /complete/");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Casting Portal</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <link rel='stylesheet' href='/resources/main.css'>
  <link href="https://fonts.googleapis.com/css?family=Droid+Sans:400,700" rel="stylesheet">
  <script type='text/javascript'>

    function post(url, data, callback) {
      var r = new XMLHttpRequest()
      var postString = ""
      for (var key in data) postString += key + "=" + data[key] + "&"
      r.open("POST", url, true)
      r.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      r.onreadystatechange = function() {
        if (r.readyState == 4) callback(r.responseText)
      }
      r.send(postString)
    }

    function parse(form) {
      data = {}
      for (var i = 0; i < form.elements.length; i++) {
        if (form.elements[i].name && form.elements[i].type != 'radio' || (form.elements[i].type == 'radio' && form.elements[i].checked)) {
          data[form.elements[i].name] = form.elements[i].value
        }
      }
      return data
    }

    function addAlert(message) {
      var e = document.createElement('div')
      e.className = "alert-container"
      e.innerHTML = "<div class='alert' onclick='dismissAlert(this.parentElement)'\">"+message+"</div>"
      document.getElementById("alerts").insertBefore(e, document.getElementById("alerts").firstChild)
      setTimeout(function() {
        e.style.height = "83px"
      }, 15)
      if (document.getElementById("alerts").children.length == 1) {
        setTimeout(function() {
          dismissAlert(e)
        }, 2500)
      }
    }

    function dismissAlert(e) {
      if (document.body.contains(e)) {
        e.style.height = "0"
        e.children[0].style.backgroundColor = "rgba(255,255,255,0)"
        e.children[0].style.color = "rgba(0,0,0,0)"
        setTimeout(function() {
          document.getElementById("alerts").removeChild(e)
          var next = document.getElementById("alerts").lastChild
          setTimeout(function() {
            dismissAlert(next)
          }, 1500)
        }, 400)
      }
    }
    </script>
</head>
<body>
  <div id="alerts"></div>
  <div id="header">
    <a href='/' style='float:left; margin-left:0'>Chapman Casting Portal</a><a></a>
    <?php if ($MYACCOUNT) echo "<a href='/create/'>Create Call</a><a href='/user/".$MYACCOUNT['username']."/'>Account</a><a href='/logout/'>Logout</a>" ?>
  </div>
  <div id="master">
