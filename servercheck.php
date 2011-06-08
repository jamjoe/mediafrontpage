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
<center>
<h2>Welcome to <a href="http://mediafrontpage.net/" target="_blank">MediaFrontPage</a>.</h2>
<?php if(false){
?>
If you have no text below, your PHP is not working.
<?php
}
else{}
$redirect = true;
echo "<p><br><br></p>";
$version = phpversion();

echo '<table border="1">';
echo "<tr><td>PHP Version $version</td><td>";if($version > 5){echo "<img src='media/green-tick.png' height='15px'/>";}else{echo "<img src='media/red-cross.png' height='15px'/>";$redirect = false;} echo "</td></tr>";
if(extension_loaded('libxml')){ 
	echo "<tr><td>LibXML found</td><td><img src='media/green-tick.png' height='15px'/></td></tr>";
}else{
	echo "<tr><td>LibXML <b>NOT</b> found</td><td><img src='media/red-cross.png' height='15px'/></td></tr>"; 
	$redirect = false;
}
if(extension_loaded('curl')){ 
	echo "<tr><td>cURL found </td><td><img src='media/green-tick.png' height='15px'/></td></tr>";
}else{ 
	echo "<tr><td>cURL <b>NOT</b> found</td><td><img src='media/red-cross.png' height='15px'/></td></tr>"; 
	$redirect = false;
}
if (file_exists('config.php')){
	echo "<tr><td>config.php found. </td><td><img src='media/green-tick.png' height='15px'/></td></tr>";
}else{
	echo "<tr><td>config.php <b>NOT</b> found</td><td><img src='media/red-cross.png' height='15px'/></td></tr>";
	$redirect = false;
}
if (file_exists('layout.php')){
	echo "<tr><td>layout.php found";
	if(!is_writable('layout.php')){
		if(@chmod("layout.php", 0777)){
			echo " and CHMODDED</td><td><img src='media/green-tick.png' height='15px'/></td></tr>";
		}
		else{
			echo ", could not be written. Please CHMOD it.</td><td><img src='media/red-cross.png' height='15px'/></td></tr>";
			$redirect = false;
		}
	}
	else{
		echo ", writeable</td><td><img src='media/green-tick.png' height='15px'/></td></tr>";
	}
}else{
	echo '<tr><td>default-layout.php';
	if(file_exists("default-layout.php")){
		if(rename("default-layout.php", "layout.php")){
			echo " renamed successfully";
		}
	}
	else{
		echo " could not be found";
		$redirect = false;
	}	
	if(file_exists("layout.php")){
		if(!is_writable('layout.php')){
			if(@chmod("layout.php", 0777)){
				echo " and CHMODDED</td><td><img src='media/green-tick.png' height='15px'/></td></tr>";
			}
			else{
				echo ", could not be written. Please CHMOD it.</td><td><img src='media/red-cross.png' height='15px'/></td></tr>";
				$redirect = false;
			}
		}
		else{
			echo ", writeable</td><td><img src='media/green-tick.png' height='15px'/></td></tr>";
		}
	}
}
echo '</table>';
if($redirect){
	echo "<p>Congratulations! Redirecting to MediaFrontPage in 5 seconds.</p>";
	echo "<script>setTimeout('redirect()', 5000);</script>";
	if (file_exists('firstrun.php')){unlink('firstrun.php');}
} else {
	echo "<p>It looks like some problems were found, please fix them then <input type=\"button\" value=\"reload\" onClick=\"window.location.reload()\"> the page.</p>";
	echo "<p>If further assistance is needed, please visit the <a href='http://forum.xbmc.org/showthread.php?t=83304' target='_blank'>forum</a> or our <a href='http://mediafrontpage.lighthouseapp.com' target='_blank'>project page</a>.</p>";
	echo "Attention WINDOWS users, please remember our WEB Server of choice for your platform is <a href='http://www.uniformserver.com/' target='_blank'>The Uniform Server</a>.";
}
?>
</center>
</body>
</html>