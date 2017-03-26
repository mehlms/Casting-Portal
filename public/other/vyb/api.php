<?php
$db_opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$db = new PDO('mysql:host=localhost;dbname=vyb;charset=utf8', "root", "", $db_opt);

$func = get("func");
$MYACCOUNT = null;
if (get("token")) {
  $token = get("token");
  $MYACCOUNT = $db->query("SELECT * FROM accounts WHERE token='$token'")->fetch();
}

if ($func == "register") {
  $spotify_id = get("spotify_id");
  $soundcloud_id = get("soundcloud_id");
  $display_name = get("display_name");

  if ($display_name && ($spotify_id || $soundcloud_id)) {
    $existenceCheck = $db->query("SELECT * FROM accounts WHERE spotify_id='$spotify_id' OR soundcloud_id='$soundcloud_id'")->fetch();
    if ($existenceCheck) {
      echo json_encode(array("status"=>"ok", "vyb_token"=>$existenceCheck["token"]));
    } else {
      $token = sha1(time().rand());
      $db->query("INSERT INTO accounts VALUES (null, '$spotify_id', '$soundcloud_id', '$display_name', 0, 0, '$token', NOW())");
      echo json_encode(array("status"=>"ok", "vyb_token"=>$token));
    }
  } else {
    echo json_encode(array("status"=>"failed"));
  }
}

else if ($MYACCOUNT && $func == "updateAccount") {
  $token = $MYACCOUNT['token'];
  $spotify_id = get("spotify_id");
  $soundcloud_id = get("soundcloud_id");
  $display_name = get("display_name");
  $lat = getDouble("lat");
  $lng = getDouble("lng");

  if ($display_name && $spotify_id && $soundcloud_id) {
    $db->query("UPDATE accounts SET spotify_id='$spotify_id', soundcloud_id='$soundcloud_id', display_name='$display_name' WHERE token='$token'");
    echo json_encode(array("status"=>"ok"));
  } else if ($lat && $lng) {
    $db->query("UPDATE accounts SET lat=$lat, lng=$lng WHERE token='$token'");
    echo json_encode(array("status"=>"ok"));
  } else {
    echo json_encode(array("status"=>"failed"));
  }
}

else if ($MYACCOUNT && $func == "startStream") {
  $token = $MYACCOUNT['token'];
  $streamer_id = $MYACCOUNT['id'];
  $stream_name = get("stream_name");
  $song = get("song");
  $artist = get("artist");
  $cover_url = get("cover_url");
  $uri = get("uri");

  $db->query("UPDATE accounts SET lat=$lat, lng=$lng WHERE token='$token'");
  $db->query("INSERT INTO streams VALUES (null, $streamer_id, '$stream_name', '$song', '$artist', '$cover_url', '$uri', '', NOW())");
  echo json_encode(array("status"=>"ok"));
}

else if ($MYACCOUNT && $func == "updateStream") {
  $streamer_id = $MYACCOUNT['id'];
  $stream_name = get("stream_name");
  $song = get("song");
  $artist = get("artist");
  $cover_url = get("song");
  $uri = get("uri");

  $db->query("UPDATE streams SET stream_name='$stream_name', song='$song', artist='$artist', cover_url='$cover_url', uri='$uri' WHERE streamer_id=$streamer_id");
  echo json_encode(array("status"=>"ok"));
}

else if ($MYACCOUNT && $func == "stopStream") {
  $streamer_id = $MYACCOUNT['id'];

  $db->query("DELETE FROM listeners WHERE streamer_id=$streamer_id");
  $db->query("DELETE FROM streams WHERE streamer_id=$streamer_id");
  echo json_encode(array("status"=>"ok"));
}

else if ($MYACCOUNT && $func == "addListener") {
  $streamer_id = get('streamer_id');
  $listener_id = $MYACCOUNT['id'];

  $db->query("INSERT INTO listeners VALUES (null, $streamer_id, $listener_id)");
  echo json_encode(array("status"=>"ok"));
}

else if ($MYACCOUNT && $func == "updateListener") {
  $streamer_id = get('streamer_id');
  $listener_id = $MYACCOUNT['id'];

  $db->query("UPDATE listeners SET streamer_id=$streamer_id WHERE listener_id=$listener_id");
  echo json_encode(array("status"=>"ok"));
}

else if ($MYACCOUNT && $func == "removeListener") {
  $streamer_id = get('streamer_id');
  $listener_id = $MYACCOUNT['id'];

  $db->query("DELETE FROM listeners WHERE listener_id=$listener_id");
  echo json_encode(array("status"=>"ok"));
}

else if ($func == "getStreams") {
  $streams = $db->query("SELECT streamer_id, lat, lng, display_name, stream_name, song, artist, cover_url, uri, uri_next, start FROM streams JOIN accounts ON streams.streamer_id=accounts.id")->fetchAll();
  echo json_encode(array("streams"=>$streams));
}

else echo json_encode(array("status"=>"failed", "message"=>"That function does not exist", "func"=>$func));

function get($s) { return isset($_GET[$s]) ? addslashes(trim($_GET[$s])) : ""; }
function getWords($s) { return isset($_GET[$s]) ? ucwords(addslashes(trim($_GET[$s]))) : null; }
function getInt($s) { return isset($_GET[$s]) ? intval(addslashes(trim($_GET[$s]))) : null; }
function getDouble($s) { return isset($_GET[$s]) ? doubleval(addslashes(trim($_GET[$s]))) : null; }
