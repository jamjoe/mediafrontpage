<html>
<head>
<title>MediaFrontPage File Edit</title>
<link rel="stylesheet" type="text/css" href="css/nav.css" />
<link rel="stylesheet" type="text/css" href="css/widget.css" />
<link href="css/front.css" rel="stylesheet" type="text/css" />
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
	cursor: move;
}
#nav-menu2 {

  display: block;
  font-size:.8em;
  margin-top:0px;
  margin-left:0px;
  padding-top: 0px;
  padding-right:0px;
  overflow: hidden;
  /*width: 2000px;*/
}

#nav-menu2 ul {
  margin-right:0px;
}


#nav-menu2 li {
  display: inline;
  margin:0;
}

#nav-menu2 li a {
  color: #ccc;
  text-decoration: none;
  display: block;
  float: left;
  font-size: 10px;
  line-height: 15px;
  min-height: 10px;
  padding:0 16px 0 14px;
  position: relative;
  text-shadow: rgba(0,0,0,.5) 0px 1px 0px;
  background:url(../media/bgNavSep.png) no-repeat right center;
}

#nav-menu2 li a:hover {
  color: #FF9522;
  background-color: rgba(0,0,0,.15);
  -webkit-box-shadow:0 2px 8px rgba(0,0,0,.25) inset;
  text-shadow: rgba(255,255,255,.5) 0px 1px 10px;
}

#nav-menu2 li a:active  {
  color: #FF9522;
}
</style></head>
<body style="margin: 0; padding: 0;">
<center>
	<div style="position:fixed;">
		<div id="nav-menu2" style="background:url(media/bgNav.png);">
		  <ul>
		    <li><a href="mfpedit.php?p=config.php">Config.php</a></li>
		    <li><a href="mfpedit.php?p=layout.php">Layout.php</a></li>
		  </ul>
		</div>
		<?php
		if(isset($_GET['p'])){
		?>
		<div style="float:left;">
		  <br><br><br><br><br>
		  <input type="submit" value="Save" onclick="document.getElementById('save_file').click();">
		</div>
		<?php
		}
		?>
	</div>
	<br><br>
<?php
// set file to read
$filename='';
if(isset($_GET['p'])){
	$filename = $_GET['p'];
}
// open file
if($fh = @fopen($filename, "r")){
  // read file contents
  $data = fread($fh, filesize($filename)) or die("Could not read file!");
  // close file
  fclose($fh);
  // print file contents
if(isset($_POST['save_file']) && $_POST['save_file']) {
	$savecontent = stripslashes($_POST['savecontent']);
	$fp = @fopen($filename, "w");
	if ($fp) {
		fwrite($fp, $savecontent);
		fclose($fp);
		echo '<script>
		        alert("Successfully saved '.$filename.'");
		        window.location.reload();
		      </script>';
	}
}
$fp = @fopen($filename, "r");
$loadcontent = fread($fp, filesize($filename));
$lines = explode("\n", $loadcontent);
$count = count($lines);
$filename = htmlspecialchars($loadcontent);
fclose($fp);
$line = '';
for ($a = 1; $a < $count+1; $a++) {
	$line .= "$a\n";
}
?>
  <table class="widget" cellpadding="0" cellspacing="0" id="1">
    <tr style="cursor: move; ">
      <td align=center colspan=2 height=25><div class="widget-head">MediaFrontPage File Editor</div></td>
    <tr>
      <td align=right><form method=post action="<?php echo $_SERVER['REQUEST_URI']?>">
          <table width="50%" valign="top" border="0" cellspacing="1" cellpadding="1">
            <tr>
              <td width="50%" align="right" valign="top">
                <pre style="color:#FF9522; text-align: right; padding: 2px; overflow: auto; border: 0px groove; font-size: 10px" name="lines" cols="5" rows="<?php echo $count;?>">
                <?php echo $line;?>
                </pre>
              </td>
              <td width="100%" align="center" valign="top">
                <textarea style="border:1px solid #2C2D32; background:#2C2D32; color:#666666; text-align: left;  padding-left: 3px; overflow: auto; font-size: 10px" name="savecontent" cols="170" rows="<?php echo $count;?>" wrap="OFF"><?php echo $loadcontent?>
	              </textarea>
	            </td>
            </tr>
          </table>
          <input type="submit" id="save_file" name="save_file" value="Save">
       </form></td>
    </tr>
    </tr>
  </table>
<? 
}else{
echo "<table width='50%' class='widget'>
    <tr>
      <td colspan='2' height='25'><div class='widget-head'>MediaFrontPage File Editor</div></td>
    <tr>
      <td align=right>
        <div id=\"nav-menu\">
          <ul>
            <li><a href=\"mfpedit.php?p=config.php\">Config.php</a></li>
            <li><a href=\"mfpedit.php?p=layout.php\">Layout.php</a></li>
          </ul>
        </div>
      </td>
    </tr>
  </table>
  ";
}
?>
</body>
</html>