<html>
<head>
<title>MediaFrontPage Server Check</title>
<script type="text/javascript">
function redirect(){
	window.location = 'index.php';
}
</script>
</head>
<body>
<center>
If you have no text below, your PHP is not working.<BR>
<?php
$redirect = false;
echo "If you can read this, <B>PHP is working.</B><BR>";
$version = phpversion();
echo "PHP Version $version";
echo '<br>';
if(extension_loaded('libxml')) { echo "Lib XML found <img src='media/green-tick.png' height='15px'/>"; $redirect = true;}else{echo "Lib XML NOT found, you will need to change your php.ini <img src='media/red-cross.png' height='15px'/>"; $redirect = false;}echo '<br>';
if(extension_loaded('curl')) { echo "Curl found <img src='media/green-tick.png' height='15px'/>"; $redirect = true;} else { echo "Curl NOT found, you will need to change your php.ini <img src='media/red-cross.png' height='15px'/>"; $redirect = false;} echo '<br>';
if (file_exists('config.php')){echo "You created config.php, seems like everything is in working order. <img src='media/green-tick.png' height='15px'/>";$redirect = true;}
else if(file_exists('default-config-new.php')){echo "Please rename default-config.php to config.php <img src='media/red-cross.png' height='15px'/>";$redirect = false;}	
else{echo "It seems you have no config file! Please make sure its named config.php or re-download from <a href=\"https://github.com/MediaFrontPage/mediafrontpage\">Media Front Page</a> <img src='media/red-cross.png' height='15px'/>";$redirect = false;}echo "<br>";
if($redirect){
	echo "Congratulations! Redirecting to MediaFrontPage in 5 seconds.";
	echo "<script>setTimeout('redirect()', 5000);</script>";
	if (file_exists('firstrun.php')){unlink('firstrun.php');}
}
?>
</center>
</body>
</html>