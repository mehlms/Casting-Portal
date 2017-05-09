<?php include "db.php";
if (!$MYACCOUNT && $_SERVER['REQUEST_URI'] != "/login/") header("Location: /login/"); // IF USER IS NOT LOGGED IN -> REDIRECT TO /LOGIN/
else if ($MYACCOUNT && $MYACCOUNT['firstname'] == NULL && $_SERVER['REQUEST_URI'] != "/complete/") header("Location: /complete/");
else if ($MYACCOUNT && $MYACCOUNT['firstname'] && (strpos($_SERVER['REQUEST_URI'], 'complete') || strpos($_SERVER['REQUEST_URI'], 'login'))) header("Location: /");
$data = isset($_GET['data']) ? intval($_GET['data']) : null;
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
  <title>Casting Portal | Chapman University</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <link rel='stylesheet' href='/resources/chapman.css'>

  <script type='text/javascript'>
    function post(url, data, callback) {
      var r = new XMLHttpRequest()
      r.open("POST", url, true)
      r.onreadystatechange = function() {
        if (r.readyState == 4) callback(r.responseText)
      }
      var formData = new FormData();
      for (var key in data) formData.append(key, data[key])
      r.send(formData)
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

    function parseArray(parent) {
      var data = []
      var objects = document.getElementById(parent).querySelectorAll('[data-row]')
      for (var i = 0; i < objects.length; i++) {
        var inputs = objects[i].querySelectorAll('[data-input]')
        var object = {}
        for (var j = 0; j < inputs.length; j++) {
          object[inputs[j].getAttribute('data-input')] = inputs[j].value
        }
        data.push(object)
      }
      return JSON.stringify(data)
    }

    function addElement(parent) {
      var parent = document.getElementById(parent)
      var e = parent.querySelector("[data-row]").cloneNode(true)
      var inputs = e.querySelectorAll('[data-input]')
      for (var i = 0; i < inputs.length; i++) inputs[i].value = ""
      var add = e.querySelector('[data-add]')
      add.innerHTML = "-"
      add.onclick = function() {
        parent.removeChild(e)
      }
      parent.appendChild(e)
    }

    function addAlert(message) {
      var e = document.createElement('div')
      e.className = "alert-container"
      e.innerHTML = "<div class='alert' onclick='dismissAlert(this.parentElement)'\">"+message+"</div>"
      document.getElementById("alerts").insertBefore(e, document.getElementById("alerts").firstChild)
      setTimeout(function() {
        e.style.height = "73px"
      }, 15)
      if (document.getElementById("alerts").children.length == 1) {
        setTimeout(function() {
          dismissAlert(e)
        }, 3000)
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
          }, 3000)
        }, 400)
      }
    }

    function checkDate(ev, input) {
      input.value = input.value.replace(/[^0-9\/]/g,'')
      if (input.value.length == 2 || input.value.length == 5) {
        if (ev.keyCode == 8) input.value = input.value.substring(0, input.value.length-1)
        else input.value += "/"
      }
    }

    function checkDateTime(ev, input) {
      input.value = input.value.replace(/[^0-9\/\s:amp]/g,'')
      if (input.value.length == 2 || input.value.length == 5) {
        if (ev.keyCode == 8) input.value = input.value.substring(0, input.value.length-1)
        else input.value += "/"
      }
      if (input.value.length == 10 || input.value.length == 16) {
        if (ev.keyCode == 8) input.value = input.value.substring(0, input.value.length-1)
        else input.value += " "
      }
      if (input.value.length == 13) {
        if (ev.keyCode == 8) input.value = input.value.substring(0, input.value.length-1)
        else input.value += ":"
      }
    }

    var notifications = false
    function toggleNotifications() {
      if (notifications) {
        document.getElementById("notifications").style.width = "0px"
        document.getElementById("c_notifications").className = "c_notifications"
      } else {
        document.getElementById("notifications").style.width = "280px"
        document.getElementById("c_notifications").className = "c_notifications notif_active"
      }
      notifications = !notifications
    }

    currentPopup = null
    function togglePopup(popup) {
      if (currentPopup == null) {
        currentPopup = popup
        currentPopup.style.top = '46%'
        currentPopup.style.display = "block"
        document.getElementById("darkness").style.display = "block"
        currentPopup.style.height = 2*Math.round(currentPopup.clientHeight/2) + "px"
        setTimeout(function() {
          currentPopup.style.opacity = 1
          currentPopup.style.top = '50%'
          document.getElementById("darkness").style.opacity = 1
        }, 33)
      } else {
        currentPopup.style.top = '40%'
        currentPopup.style.opacity = 0
        document.getElementById("darkness").style.opacity = 0
        setTimeout(function() {
          document.getElementById("darkness").style.display = "none"
          currentPopup.style.display = "none"
          currentPopup = null
        }, 300)
      }
    }
  </script>
</head>
<body>
  <div id="alerts"></div>
  <div id="darkness" onclick="togglePopup(currentPopup)"></div>
  <div id="header">
    <a href='/' class="c_logo"></a>
    <?php if ($MYACCOUNT && $MYACCOUNT['firstname'] != null) {
      echo "
      <div id='notifications'>
        <h1>Notifications</h1>
      </div>
      <div class='c_notifications' onclick='toggleNotifications()' id='c_notifications'></div>
      <div class='c_search'>
        <select class='c_type'>
            <option value='0'>All</option>
            <option value='1'>Calls</option>
            <option value='2'>Talent</option>
            <option value='3'>Directors</option>
        </select>
        <input type='text' class='c_query' placeholder='Search' spellcheck='false' autocomplete='off' maxlength='40'>
      </div>
      ";
    } ?>
  </div>
  <div class="master">
