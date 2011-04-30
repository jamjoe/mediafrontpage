<?php
$wIndex["wjDownloader"]  = array("name" => "jDownloader", "type" => "ajax", "block" => "jdownloaderwrapper", "headerfunction" => "widgetjDHeader();", "call" => "widgets/wjDownloader.php?style=w", "interval" => 10000);
function widgetjDHeader() {
	echo <<< JDHEADER
		<script type="text/javascript" language="javascript">
		<!--

			function toggle(subitem){

				var array = document.getElementsByName(subitem);
				
				for( var c = 0; c < array.length; c++){
					if (array[c].style.display == 'none'){
						array[c].style.display = 'table-row';
					}
					else{
						array[c].style.display = 'none';
					}
				}
			


			}

		-->
		</script>
JDHEADER;
}

function widgetjDownloader(){
	global $jd_url;
	$jd = $jd_url;

	try{
		$speed   = @file_get_contents($jd."get/speed");
		$status  = @file_get_contents($jd."get/downloadstatus");

		$speedLimit = @file_get_contents($jd."get/speedlimit");

		$dlCount  = @file_get_contents($jd."get/downloads/currentcount");
		$dlList  =  @file_get_contents($jd."get/downloads/alllist");

		$dlList = str_replace("</jdownloader>", "", $dlList);
		$dlList = str_replace("<jdownloader>", "", $dlList);
		
		$dlList = str_replace("<package ", "|" , $dlList);
		$dlList = str_replace(">\n<file", "" , $dlList);
		$dlList = str_replace("/>\n</package>", "" , $dlList);
		$dlList = str_replace("/>", "" , $dlList);
		$dlList = str_replace("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n\n", "" , $dlList);
		$dlList = str_replace("\"", "" , $dlList);

	
		$dlList = explode("|",$dlList);

		$action="";

		switch($status){
		case 'RUNNING':
			$status="Downloading @".$speed." kB/s";
			$action = $jd."action/stop";
			break;
		case 'STOPPING':
			$status="Stopping";
			break;
		case 'NOT_RUNNING':
			$status="Stopped";
			$action = $jd."action/start";
			break;
		}

		echo "<a href='".$action."' target='nothing'>".$status."</a>";

		echo "<table border=\"0\" width='100%' style='table-layout:fixed;' cellspacing='0' cellpadding='0'><tr>";
		echo "<th width='10%'></th>";
		echo "<th width='40%'>Name</th>";
		echo "<th width='50%'>Status</th>";
		echo "</tr>";
			
		foreach($dlList as $item){
	 		if($item!=""){
		 		$item = str_replace("\n", "" , $item);
		 		$item = str_replace(" package", "|package" , $item);
		 		$item = str_replace("file_", "|file" , $item);					
				
				$original = $item;

				$item = explode("|",$item);
				//echo "<pre>";
				//print_r($item);
				//echo "</pre>";
				
				
				
				$eta    	 = substr($item[0],12);
				$links_progress = substr($item[1],24);
				$links_total = substr($item[2],19);
				$downloaded  = substr($item[3],15);
				$name		 = substr($item[4],13);
				$percent 	 = substr($item[5],16);
				$total_size  = substr($item[6],13);
				$speed   	 = substr($item[7],14);
				$to_do   	 = substr($item[8],13);

				$popup  = "";
				$popup .= "<p>Name: ".$name."</p>";
				$popup .= "<p>Active Links: ".$links_progress." of ".$links_total."</p>";
				$popup .= "<p>Progress: ".$percent."% (".$downloaded."/".$total_size.")</p>";
				
				$color = "red";
	
				if($links_progress!=0){
					$hoster = substr($item[9],11);
					//$to_do	= $item->attributes()->package_todo;
					$popup .= "<p>Hoster: ".$hoster."</p>";
					$color  = "green";
				}
				
				$more = ($links_total>1)?"<img width='10px' src='media/btnAdd.png' style:'vertical-align:middle;' onClick=\"toggle('".$name."');\" />":"";

				//echo "$eta $links_progress $links_total $downloaded $name $percent $total_size $speed $to_do";


				echo "<tr>";
				echo "<td>".$more."</td>";
				echo "<td><font color=".$color."><div style='text-overflow:ellipsis;overflow:hidden; white-space:nowrap;' onMouseOver=\"ShowPopupBox('".$popup."');\" onMouseOut=\"HidePopupBox();\">".$name."</div></font></td>";
				echo "<td><div class=\"queueitem\">";
				echo "\t\t\t\t<div class=\"progressbar\">";
				echo "\t\t\t\t\t<div class=\"progress\" style=\"width:".$percent."%\"></div>";
				echo "\t\t\t\t\t<div class=\"progresslabel\" style='text-align: center;'>".$to_do." left @ ".$speed." - ".$eta."</div>";
				echo "\t\t\t\t</div><!-- .progressbar -->";
				echo "\t\t\t</div><!-- .queueitem -->";
				echo "</td>";
				echo "</tr>";

				$subitem = explode("filename=",$original);
				
/*
				echo "<pre>";
				print_r($subitem);
				echo "</pre>";
*/

				for($x=1; $x<sizeof($subitem);$x+=1){
					
					$s = explode("|",$subitem[$x]);
					
					$subname 	= substr($s[0],0);
					$subperc 	= floor(substr($s[2],12));
					$subspeed	= substr($s[3],10);
					$substatus	= substr($s[4],11);
					$subhoster 	= (!empty($substatus))?substr($s[5],11):"";
					$subcolor 	= (!empty($substatus) && $substatus!="/ ")?"green":"red";
					
					$substatus = ($subperc>=100)?"Finished":$substatus;
					
					echo "<tr style='display:none;' name='".$name."'>";
					echo "<td>$x</td>";
					echo "<td><font color=".$subcolor."><div style='text-overflow:ellipsis;overflow:hidden; white-space:nowrap;' >".$subname."</div></font></td>";
					echo "<td><div class=\"queueitem\">";
					echo "\t\t\t\t<div class=\"progressbar\">";
					echo "\t\t\t\t\t<div class=\"progress\" style=\"width:".$subperc."%\"></div>";
					echo "\t\t\t\t\t<div class=\"progresslabel\" style='text-align: center;'>$substatus</div>";
					echo "\t\t\t\t</div><!-- .progressbar -->";
					echo "\t\t\t</div><!-- .queueitem -->";
					echo "</td>";
					echo "</tr>";

				}
			}
		}
		echo "</table>";
	
		//$dlList = explode("package",$dlList);
		//$dlList = explode("file",$dlList);

		//print_r($dlList);
		
	}
	catch(Exception $e){
		echo "Error: $e";
	}
}

if(!empty($_GET['style']) && ($_GET['style'] == "w")) {
	require_once "../config.php";
	if($_GET['style'] == "w") {
?>
<html>
	<head>
		<title>Media Front Page - jDownloader</title>
		<link rel='stylesheet' type='text/css' href='css/front.css'>
	</head>
	<body>
					<iframe name="nothing" height="0" width="0" style="visibility:hidden;display:none;"></iframe>
<?php
		widgetjDownloader();
?>
	</body>
</html>
<?php
	} else {
		widgetjDownloader();
	}
}
?>
