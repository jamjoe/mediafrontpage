<html>
<head>
<title>Media Center</title>
<link rel="shortcut icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="css/front.css" />
<style type="text/css">
.widget {
    border:1px solid black;
    -moz-border-radius:6px 6px 6px 6px;
    border-radius:6px 6px 6px 6px;
    margin:0px 0px;
    box-shadow: 3px 3px 3px #000;
	background:#2C2D32;
}
.widget-head {
    -moz-border-radius:6px 6px 0px 0px;
    border-radius:6px 6px 0px 0px;
    background:#3d3d3d;
    border-bottom:1px solid black;
    width: 100%;
    height: 30px;
    line-height: 30px;
	font-weight:bold;
}
</style>
<?php
require_once("config.php");
If (!$authsecured) {
    header('Location: index.php');
    exit;
}
if (isset($_POST['user']) && isset($_POST['password'])) {
    if ($_POST['user']==$authusername && $_POST['password']==$authpassword) {
        // OK
        $_SESSION['loggedin'] = true;
        header('Location: index.php');
        exit;
    } else {
echo "<center><br><br><br>";
echo "<form action=\"auth.php\" method=\"post\">";
echo "<table class=\"widget\" width=259 cellpadding=0 cellspacing=0 id=1>";
echo "<tr style=cursor: move;>";
echo "<td align=center height=25><div class=\"widget-head\">MediaFrontPage Authentication</div></td>";
echo "<tr>";
echo "<td align=center height=25><br>Invalid Username and/or Password.</td>";
echo "<tr>";
echo "<td align=center height=25>&nbsp; &nbsp;</td>";
echo "<tr>";
echo "<td align=center height=25>Please try again.</td>";
echo "<tr>";
echo "<td align=center height=25>";
echo "<input type=\"button\" value=\"<< Back\" onclick=\"history.go(-1);return false;\" /><br><br>";
echo "</td>";
echo "</table>";
echo "</form>";
echo "</center>";
    }
} else {
    // not logging in
    header('Location: login.php');
    exit;
}
?>