<?php
echo "<center>";
echo  "<br>";
echo "<br><br>";

echo"<form action=\"auth.php\" method=\"post\">";

echo   "<table width=259 cellpadding=3 cellspacing=0 id=1>";
echo      "<tr>";
echo        "<td align=center colspan=2 height=25>Media Front Page Authentication</td>";

echo"<tr>";
echo        "<td align=left>&nbsp; &nbsp;Username:</td>";
echo"<td align=center>";
echo          "<input type='text' name=\"user\" size=15 />";
echo        "</td>";

echo"<tr>";
echo       "<td align=left>&nbsp; &nbsp;Password:</td>";
echo"<td align=center>";
echo         "<input type='password' name=\"password\" size=15 />";
echo        "</td>";

echo"<tr>";
echo       "<td align=center colspan=2>&nbsp;</td>";

echo"<tr>";
echo"<td align=center colspan=2>";
echo         "<input type='submit' value='Log in' />";
echo        "</td>";

echo"</table>";
echo"</form>";

echo"</center>";
?>
<html>
<head>
<title>Media Center</title>
<link rel="shortcut icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="css/front.css" />
</head>

</html>
