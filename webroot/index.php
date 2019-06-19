<?php
    define('GUEST', 23);
    include_once('config.php');
    include_once('helper.php');

    sstart();
?>

<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="BRAVE Slack">
    <meta name="author" content="kiu Nakamura">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="shortcut icon" href="favicon.png">
    <title>BRAVE Slack</title>
    <link href="css/bootstrap-cyborg.css" rel="stylesheet">
    <link href="css/slack.css" rel="stylesheet"> 
  </head>

<body>

    <script src="js/jquery-1.11.3.min.js"></script>

<!-- CONTENT -->

<?php
    if (serror()) {
	include('inc_error.php');
    } else if (svalid()) {
	include('inc_success.php');
    } else {
	include('inc_start.php');
    }
?>

<!-- CONTENT -->

    <div style="font-size:70%; position:fixed; bottom:1px; right:5px; z-index:23;">Brought to you by <a href="http://evewho.com/pilot/kiu+Nakamura" target="_blank">kiu Nakamura</a> / <a href="http://evewho.com/alli/Brave+Collective" target="_blank">Brave Collective</a></div>

    <script src="js/bootstrap.min.js"></script>

</body>
</html>
