<?php include "db.php";
if (!$MYACCOUNT && $_SERVER['REQUEST_URI'] != "/login/") header("Location: /login/"); // IF USER IS NOT LOGGED IN -> REDIRECT TO /LOGIN/
else if ($MYACCOUNT && $MYACCOUNT['firstname'] == NULL && $_SERVER['REQUEST_URI'] != "/complete/") header("Location: /complete/");
else if ($MYACCOUNT && $MYACCOUNT['firstname'] && (strpos($_SERVER['REQUEST_URI'], 'complete') || strpos($_SERVER['REQUEST_URI'], 'login'))) header("Location: /");
$data = isset($_GET['data']) ? intval($_GET['data']) : null;
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
  <title>Chapman Casting</title>
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
        if (form.elements[i].name) {
          if (form.elements[i].type == 'file') {
            data[form.elements[i].name] = form.elements[i].files[0]
          } else if (form.elements[i].type == 'checkbox') {
            if (form.elements[i].checked) data[form.elements[i].name] = 1
            else data[form.elements[i].name] = 0
          } else {
            data[form.elements[i].name] = form.elements[i].value
          }
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
        var gotValue = false
        for (var j = 0; j < inputs.length; j++) {
          if (inputs[j].value != "" && inputs[j].value != "0") gotValue = true
          object[inputs[j].getAttribute('data-input')] = inputs[j].value
        }
        if (gotValue) data.push(object)
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

    var notifications = false
    function toggleNotifications(button) {
      if (notifications) {
        document.getElementById("notifications").style.right = "-270px"
        button.className = "c_notifications"
      } else {
        getNotifications()
        document.getElementById("notifications").style.right = "0px"
        button.className = "c_notifications c_active"
      }
      notifications = !notifications
    }

    currentPopup = null
    function togglePopup(popup) {
      if (currentPopup == null) {
        currentPopup = popup
        currentPopup.style.top = '47%'
        currentPopup.style.display = "block"
        document.getElementById("darkness").style.display = "block"
        var heightObj = currentPopup
        if (heightObj.getElementsByClassName('card').length > 0) heightObj = currentPopup.getElementsByClassName('card')[0]
        currentPopup.style.height = 2*Math.round(heightObj.clientHeight/2) + "px"
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
    function search() {
      var query = document.getElementById('search_query').value
      post("/resources/ajax/functions.php", {'func': 'search', 'query': query}, function(r) {
        r = JSON.parse(r)
        document.getElementById('search').innerHTML = ""
        r['results'].forEach(function(d) {
          if (d['type'] == 1) document.getElementById('search').innerHTML += "<a onclick='getCall("+d['id']+")'><div class='c_pic' "+(d['url'] ? "style='background-image:url(/resources/assets/posters/" + d['url'] + ")'" : "")+"></div><div class='c_text'>Call<br><b>"+d['title']+"</b></div></a>"
          else if (d['type'] == 2) document.getElementById('search').innerHTML += "<a href='/user/"+d['d_id']+"/'><div class='c_pic' "+(d['url'] ? "style='background-image:url(/resources/assets/profile/" + d['url'] + ")'" : "")+"></div><div class='c_text'>Director<br><b>"+d['title']+"</b></div></a>"
          else if (d['type'] == 3) document.getElementById('search').innerHTML += "<a href='/user/"+d['a_id']+"/'><div class='c_pic' "+(d['url'] ? "style='background-image:url(/resources/assets/profile/" + d['url'] + ")'" : "")+"></div><div class='c_text'>Talent<br><b>"+d['title']+"</b></div></a>"
        })
      })
    }

    // POPUPS

    function getCall(id) {
      post("/resources/ajax/functions.php", {"func": "getCall", "id": id}, function(r) {
        r = JSON.parse(r)
        if (r["status"] == "ok") {
          var popup = document.getElementById("popup_call")

          popup.querySelector("[data-poster]").style.backgroundImage = "url('/resources/images/poster.png')"
          if (r['call']['poster']) popup.querySelector("[data-poster]").style.backgroundImage = "url(/resources/assets/posters/" + r['call']['poster'] + ")"
          popup.querySelector("[data-script]").disabled = true
          if (r['call']['script']) {
            popup.querySelector("[data-script]").disabled = false
            popup.querySelector("[data-script]").onclick = function() {
              window.open("/resources/assets/scripts/" + r['call']['script'], '_blank')
            }
          }

          popup.querySelector("[data-title]").innerHTML = r['call']['title']
          popup.querySelector("[data-genre]").innerHTML = r['call']['g1']
          if (r['call']['g2']) popup.querySelector("[data-genre]").innerHTML += ", " + r['call']['g2']
          popup.querySelector("[data-class]").innerHTML = r['call']['class']
          popup.querySelector("[data-storyline]").innerHTML = r['call']['storyline']

          popup.querySelector("[data-collaborators]").innerHTML = ""
          r['collaborators'].forEach(function(d) {
            popup.querySelector("[data-collaborators]").innerHTML += "<div class='c_text'><a href='/user/"+d['d_id']+"/'>"+d['firstname']+" "+d['lastname']+"</a></div>"
          })
          popup.querySelector("[data-auditions]").innerHTML = ""
          r['auditions'].forEach(function(d) {
            popup.querySelector("[data-auditions]").innerHTML += "<div class='row'> \
                                                                    <div class='label' style='width: 205px'> \
                                                                      <div class='c_text'>"+d['audition_time']+"</div> \
                                                                    </div> \
                                                                    <div class='label'> \
                                                                      <div class='c_text'>"+d['audition_place']+"</div> \
                                                                    </div> \
                                                                  </div>"
          })
          popup.querySelector("[data-shootings]").innerHTML = ""
          r['shootings'].forEach(function(d) {
            popup.querySelector("[data-shootings]").innerHTML += "<div class='row'> \
                                                                    <div class='label' style='width: 205px'> \
                                                                      <div class='c_text'>"+d['shooting_from']+"</div> \
                                                                    </div> \
                                                                    <div class='label'> \
                                                                      <div class='c_text'>"+d['shooting_to']+"</div> \
                                                                    </div> \
                                                                  </div>"
          })
          popup.querySelector("[data-characters]").innerHTML = ""
          r['characters'].forEach(function(d) {
            popup.querySelector("[data-characters]").innerHTML += "<div class='row'> \
                                                                    <div class='label' style='width:160px'> \
                                                                      <div class='c_text'><b>"+d['name']+"</b></div> \
                                                                    </div> \
                                                                    <div class='label' style='width:75px'> \
                                                                      <div class='c_text'>"+d['min']+"</div> \
                                                                    </div> \
                                                                    <div class='label' style='width:75px'> \
                                                                      <div class='c_text'>"+d['max']+"</div> \
                                                                    </div> \
                                                                    <div class='label' style='width:173px; position: relative'> \
                                                                      <input type='button' style='top: -11px' class='c_edit "+(d['interested'] ? 'interested' : '')+"' value='Interested' onclick='interested(this, "+d['id']+")' "+(d['can_interested'] ? '' : 'disabled')+"> \
                                                                      <div class='c_text'>"+(d['gender']==1?"Male":d['gender']==2?"Female":"Any Gender")+"</div> \
                                                                    </div> \
                                                                  </div> \
                                                                  <div class='c_text'><p style='width: 405px'>"+d['description']+"</p></div>"
          })
          togglePopup(popup)
        } else addAlert(r['message'])
      })
    }
    function interested(sender, char_id) {
      post("/resources/ajax/functions.php", {"func": "interested", "char_id": char_id}, function(r) {
        r = JSON.parse(r)
        if (r['status'] == 'ok' && r['interested']) sender.className = "c_edit interested"
        else if (r['status'] == 'ok' && !r['interested']) sender.className = "c_edit"
        else addAlert(r['message'])
      })
    }
    function getNotifications() {
      post("/resources/ajax/functions.php", {"func": "getNotifications"}, function(r) {
        r = JSON.parse(r)
        document.getElementById("notifications").querySelector("[data-notifications]").innerHTML = "";
        r['notifications'].forEach(function(d) {
          if (d['type'] == 1 && d['heart'] == 1) {
            document.getElementById("notifications").querySelector("[data-notifications]").innerHTML += "<a href='/user/"+d['id']+"/'><b>"+d['firstname']+" "+d['lastname']+"</b><br> recommends you</a>"
          } else if (d['type'] == 1) {
            document.getElementById("notifications").querySelector("[data-notifications]").innerHTML += "<a href='/user/"+d['id']+"/'><b>"+d['firstname']+" "+d['lastname']+"</b> said<br> \""+d['comment']+"\"</a>"
          } else if (d['type'] == 2) {
            document.getElementById("notifications").querySelector("[data-notifications]").innerHTML += "<a href='/user/"+d['id']+"/'><b>"+d['firstname']+" "+d['lastname']+"</b> is<br> interested in \""+d['name']+"\"</a>"
          }
        })
      })
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
      <div class='c_notifications' onclick='toggleNotifications(this)'></div>
      <div id='notifications'>
        <h1>Notifications</h1>
        <div data-notifications></div>
      </div>
      <div class='c_search'>
        <input type='text' class='c_query' placeholder='Search' spellcheck='false' autocomplete='off' maxlength='40' onkeyup='search()' id='search_query'>
        <div id='search'></div>
      </div>
      ";
    } ?>
  </div>
  <div class="master">
    <div id="popup_call" class='popup'>
      <div class="card">
        <h1>Casting Call · <span data-title></span></h1>
        <div class="call_poster" data-poster></div>
        <div class="label">
          <p>Class</p>
          <div class='c_text' data-class></div>
        </div>
        <div class="label" style='width: 205px'>
          <p>Genre</p>
          <div class='c_text' data-genre></div>
        </div>
        <div class="label">
          <p>Collaborators</p>
          <div data-collaborators></div>
        </div>
        <div class='row'>
          <div class="label" style='width: 205px'>
            <p>Audition Time</p>
          </div>
          <div class="label">
            <p>Place</p>
          </div>
        </div>
        <div data-auditions></div>
        <div class='row'>
          <div class="label" style='width: 205px'>
            <p>Shooting Dates From</p>
          </div>
          <div class="label">
            <p>To</p>
          </div>
        </div>
        <div data-shootings></div>
        <div class="label">
          <p>Storyline</p>
          <div class='c_text' data-storyline></div>
        </div>
        <h2>CHARACTERS</h2>
        <hr>
        <div class='row'>
          <div class="label" style="width:160px">
            <p>Name</p>
          </div>
          <div class="label" style="width:75px">
            <p>Min Age</p>
          </div>
          <div class="label" style="width:75px">
            <p>Max Age</p>
          </div>
          <div class="label" style="width:170px">
            <p>Gender</p>
          </div>
        </div>
        <div data-characters></div>
        <input type='button' value='Download Script' data-script disabled><input type='button' value='Print Preview' onclick='window.print()'>
      </div>
    </div>
