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
	</style>
	<script language="javascript" type="text/javascript" src="js/editArea/edit_area_compressor.php?plugins"></script>
	<script language="javascript" type="text/javascript">
	editAreaLoader.init({
		id : "textarea_1",		// textarea id
		syntax: "php",			// syntax to be uses for highgliting
		start_highlight: true		// to display with highlight mode on start-up
	});
	</script>
</head>
<body>
    <center>
      <div style="width:90%; height:95%;" align="center" class="widget">
        <div class="widget-head">MediaFrontPage File Editor</div>
	      <div id="nav-menu2">
		<div id="nav-menu2" style="background:url(media/bgNav.png);">
	        <ul>
          <?php
          if(isset($_GET['p'])){
            echo '<li><a href="#" onclick="document.getElementById(\'save_file\').click();">Save</a></li>
                  <li><a href="#" onclick="window.location.reload();">Cancel</a></li>';
          } else {
            echo '<li><a>NO FILE SELECTED. PLEASE SELECT A FILE TO EDIT.</a></li>'; 
          }
          ?>
          </ul>
          <ul style="float: left;">
            <li><a href="mfpedit.php?p=config.php">Configuration</a></li>
            <li><a href="mfpedit.php?p=layout.php">Layout</a></li>
          </ul>
        </div>
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
        <form method=post action="<?php echo $_SERVER['REQUEST_URI'] ?>" >
          <textarea id="textarea_1" name="savecontent" style="padding:0; margin:0; width:100%; height:85%;"><?php echo $loadcontent ?></textarea>
          <input type="submit" id="save_file" name="save_file" value="Save" style="display: none;">
        </form>
<?php
}
?>
      </div>
    </center>
  </body>
</html>