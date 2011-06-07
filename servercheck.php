<html>
<head>
<title>MediaFrontPage Server Check</title>
<script type="text/javascript">
function redirect(){
	window.location = 'index.php';
}
</script>
<link href="css/front.css" rel="stylesheet" type="text/css" />	
</head>
<body>
<h1>Welcome to Media Front Page.</h1>
<center>
<?php if(false){
?>
If you have no text below, your PHP is not working.
<?php
}
else{}
$redirect = true;
echo "<p>If you can read this, <B>PHP is working.</B></p>";
$version = phpversion();
echo "<p>PHP Version $version</p>";
echo '<table border="1">';
if(extension_loaded('libxml')){ 
	echo "<tr><td>Lib XML found</td><td><img src='media/green-tick.png' height='15px'/></td></tr>";
}else{
	echo "<tr><td>Lib XML NOT found, you will need to change your php.ini </td><td><img src='media/red-cross.png' height='15px'/></td></tr>"; 
	$redirect = false;
}
if(extension_loaded('curl')){ 
	echo "<tr><td>cURL found </td><td><img src='media/green-tick.png' height='15px'/></td></tr>";
}else{ 
	echo "<tr><td>cURL <b>NOT</b> found</td><td><img src='media/red-cross.png' height='15px'/></td></tr>"; 
	$redirect = false;
}
if (file_exists('config.php')){
	echo "<tr><td>You created config.php, seems like everything is in working order. </td><td><img src='media/green-tick.png' height='15px'/></td></tr>";
} else if(file_exists('default-config-new.php')){
	echo "<tr><td>Please rename default-config.php to config.php </td><td><img src='media/red-cross.png' height='15px'/></td></tr>";
	$redirect = false;
}else{
	echo "<tr><td>It seems you have no config file! Please make sure its named config.php or re-download from <a href=\"https://github.com/MediaFrontPage/mediafrontpage\">Media Front Page</a> </td><td><img src='media/red-cross.png' height='15px'/></td></tr>";
	$redirect = false;
}
echo '</table>';
if($redirect){
	echo "<p>Congratulations! Redirecting to MediaFrontPage in 5 seconds.</p>";
	echo "<script>setTimeout('redirect()', 5000);</script>";
	if (file_exists('firstrun.php')){unlink('firstrun.php');}
}
?>
</center>
</body>
</html>