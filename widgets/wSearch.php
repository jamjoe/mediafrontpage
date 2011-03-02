<?php
$wdgtSearch = array("name" => "NZBMatrix", "type" => "inline", "function" => "widgetSearch();", "headerfunction" => "widgetSearchHeader();");
$wIndex["wSearch"] = $wdgtSearch;

function widgetSearchHeader() {
	echo <<< SEARCHHEADER
<script type="text/javascript" language="javascript">
	<!--
		function catDropDown(str) {
			if(str==1){
				document.getElementById('type').innerHTML="<option value=\"\">Everything</option><option  class=\"grouping\" value=\"1000\">Console</option><option  value=\"1010\">&nbsp;&nbsp;NDS</option><option  value=\"1080\">&nbsp;&nbsp;PS3</option><option  value=\"1020\">&nbsp;&nbsp;PSP</option><option  value=\"1030\">&nbsp;&nbsp;Wii</option><option  value=\"1060\">&nbsp;&nbsp;WiiWare/VC</option><option  value=\"1070\">&nbsp;&nbsp;XBOX 360 DLC</option><option  value=\"1040\">&nbsp;&nbsp;Xbox</option><option  value=\"1050\">&nbsp;&nbsp;Xbox 360</option><option  class=\"grouping\" value=\"2000\">Movies</option><option  value=\"2010\">&nbsp;&nbsp;Foreign</option><option  value=\"2040\">&nbsp;&nbsp;HD</option><option  value=\"2020\">&nbsp;&nbsp;Other</option><option  value=\"2030\">&nbsp;&nbsp;SD</option><option  class=\"grouping\" value=\"3000\">Audio</option><option  value=\"3030\">&nbsp;&nbsp;Audiobook</option><option  value=\"3040\">&nbsp;&nbsp;Lossless</option><option  value=\"3010\">&nbsp;&nbsp;MP3</option><option  value=\"3020\">&nbsp;&nbsp;Video</option><option  class=\"grouping\" value=\"4000\">PC</option><option  value=\"4010\">&nbsp;&nbsp;0day</option><option  value=\"4050\">&nbsp;&nbsp;Games</option><option  value=\"4020\">&nbsp;&nbsp;ISO</option><option  value=\"4030\">&nbsp;&nbsp;Mac</option><option  value=\"4040\">&nbsp;&nbsp;Phone</option><option  class=\"grouping\" value=\"5000\">TV</option><option  value=\"5020\">&nbsp;&nbsp;Foreign</option><option  value=\"5040\">&nbsp;&nbsp;HD</option><option  value=\"5050\">&nbsp;&nbsp;Other</option><option  value=\"5030\">&nbsp;&nbsp;SD</option><option  value=\"5060\">&nbsp;&nbsp;Sport</option><option  class=\"grouping\" value=\"6000\">XXX</option><option  value=\"6010\">&nbsp;&nbsp;DVD</option><option  value=\"6020\">&nbsp;&nbsp;WMV</option><option  value=\"6030\">&nbsp;&nbsp;XviD</option><option  value=\"6040\">&nbsp;&nbsp;x264</option><option  class=\"grouping\" value=\"7000\">Other</option><option  value=\"7030\">&nbsp;&nbsp;Comics</option><option  value=\"7020\">&nbsp;&nbsp;Ebook</option><option  value=\"7010\">&nbsp;&nbsp;Misc</option>"
			}
			else if(str==2){
				document.getElementById('type').innerHTML="<option value=\"0\">Everything</option><option disabled value=\"Movies\" style=\"font-weight: bold;\">Movies: ALL</option><option value=\"1\">Movies: DVD</option><option value=\"2\">Movies: Divx/Xvid</option><option value=\"54\">Movies: BRRip</option><option value=\"42\">Movies: HD (x264)</option><option value=\"50\">Movies: HD (Image)</option><option value=\"48\">Movies: WMV-HD</option><option value=\"3\">Movies: SVCD/VCD</option><option value=\"4\">Movies: Other</option><option disabled value=\"tv-all\" style=\"font-weight: bold;\">TV: ALL</option><option value=\"5\">TV: DVD</option><option value=\"6\">TV: Divx/Xvid</option><option value=\"41\">TV: HD</option><option value=\"7\">TV: Sport/Ent</option><option value=\"8\">TV: Other</option><option disabled value=\"Documentaries\" style=\"font-weight: bold;\">Documentaries: ALL</option><option value=\"9\">Documentaries: STD</option><option value=\"53\">Documentaries: HD</option><option disabled value=\"games-all\" style=\"font-weight: bold;\">Games: ALL</option><option value=\"10\">Games: PC</option><option value=\"11\">Games: PS2</option><option value=\"43\">Games: PS3</option><option value=\"12\">Games: PSP</option><option value=\"13\">Games: Xbox</option><option value=\"14\">Games: Xbox360</option><option value=\"56\">Games: Xbox360 (Other)</option><option value=\"15\">Games: PS1</option><option value=\"16\">Games: Dreamcast</option><option value=\"44\">Games: Wii</option><option value=\"51\">Games: Wii VC</option><option value=\"45\">Games: DS</option><option value=\"46\">Games: GameCube</option><option value=\"17\">Games: Other</option><option disabled value=\"apps-all\" style=\"font-weight: bold;\">Apps: ALL</option><option value=\"18\">Apps: PC</option><option value=\"19\">Apps: Mac</option><option value=\"52\">Apps: Portable</option><option value=\"20\">Apps: Linux</option><option value=\"55\">Apps: Phone</option><option value=\"21\">Apps: Other</option><option disabled value=\"music-all\" style=\"font-weight: bold;\">Music: ALL</option><option value=\"22\">Music: MP3 Albums</option><option value=\"47\">Music: MP3 Singles</option><option value=\"23\">Music: Lossless</option><option value=\"24\">Music: DVD</option><option value=\"25\">Music: Video</option><option value=\"27\">Music: Other</option><option value=\"28\" style=\"font-weight: bold;\">Anime: ALL</option><option disabled value=\"other-all\" style=\"font-weight: bold;\">Other: ALL</option><option value=\"49\">Other: Audio Books</option><option value=\"33\">Other: Emulation</option><option value=\"34\">Other: PPC/PDA</option><option value=\"26\">Other: Radio</option><option value=\"36\">Other: E-Books</option><option value=\"37\">Other: Images</option><option value=\"38\">Other: Mobile Phone</option><option value=\"39\">Other: Extra Pars/Fills</option><option value=\"40\">Other: Other</option>"
			}
		}
	-->
</script>

SEARCHHEADER;
}


function widgetSearch() {
	global $nzbusername, $nzbapi,$saburl,$sabapikey;
	
	if(empty($_POST['search'])){
		$_POST['site']=1;
		echo getform();
	}
	else{
		$item = $_POST['search'];
		echo getform();
		
		if($_POST['site']==1){
			echo nzbsu($item);
		}
		elseif($_POST['site']==2){
			echo nzbmatrix($item);
		}	
	}
}



function nzbsu($item) {
	global $saburl,$sabapikey, $nzbsuapi, $nzbsudl;
		$type = "";
		if(!empty($_POST['type'])){
			$type = "&cat=".$_POST['type'];
		}

		$table = "<div style=\"height:70%;overflow:auto;\"><table border='2'><tr><th></th><th>Name</th><th>Size</th><th>Category</th></tr>";
		$search = "http://nzb.su/api?t=search&q=".urlencode($item).$type."&apikey=".$nzbsuapi."&o=json";
		$json = @file_get_contents($search);
		$content = json_decode($json, true);
		//print_r($content);
		
		foreach($content as &$array){
			//print_r($array);
			$id = $array[guid];
			$name = $array[name];
			$cat = $array[category_name];
			$size = to_readable_size($array[size]);
			$postdate = "<p>Date Posted: ".$array[postdate]."</p>";
			$coments = "<p>Coments: ".$array[comments]."</p>";
			$group_name = "<p> Group name: ".$array[group_name]."</p>";
			$grabs = "<p> Grabs: ".$array[grabs]."</p>";
			(!empty($array[seriesfull]))?($seriesfull=("<p>Episode info: ".$array[seriesfull]." ".$array[tvtitle]." ".$array[tvairdate]."</p>")):($seriesfull="");

			$url="http://nzb.su/getnzb/".$id.".nzb".$nzbsudl;
			$url=$saburl.'api?mode=addurl&name='.urlencode($url).'&apikey='.$sabapikey;
			$nzblink = "http://nzb.su/details/".$id;
			$name = str_replace(".", "\n", $name);
			$name = str_replace(" ", "\n", $name);
			$item_desc = $postdate.$coments.$group_name.$grabs.$seriesfull;
			$item_desc = str_replace("\n", "<br>", $item_desc);
			
			if(strlen($name)!=0){
			$table .="\n<tr><td><a href=$url; target='nothing';><img class=\"sablink\" src=\"./media/sab2_16.png\" alt=\"Download with SABnzdd+\"/></a></td>
					 <td style='width:60%'><a href=\"$nzblink\" onMouseOver=\"ShowPopupBox('".$item_desc."');\" onMouseOut=\"HidePopupBox();\">".$name."</a></td>
					 <td>".$size."</td>
					 <td style='width:20%'>".$cat."</td></tr>";
			}
		}
		$table .= "</table></div>";
		return $table;

}


function nzbmatrix($item) {
	global $nzbusername, $nzbapi,$saburl,$sabapikey;
		$type = "";
		if(!empty($_POST['type'])){
			$type = "&catid=".$_POST['type'];
		}
		$search = "http://api.nzbmatrix.com/v1.1/search.php?search=".urlencode($item).$type."&username=".$nzbusername."&apikey=".$nzbapi;
		$content = file_get_contents($search);
		$itemArray = explode('|',$content);
		$table = "<div style=\"height:70%;overflow:auto;\"><table border='2'><tr><th></th><th>Name</th><th>Size</th><th>Category</th></tr>";
			foreach($itemArray as &$item){
					$item = explode(';',$item);
/*
					foreach($item as &$value){
					echo $value;
					echo "</br>";
					}
*/					
					$id = $item[0];
					$name = substr($item[1],9);
					$link = $item[2];
					$size = 0+substr($item[3], 6);
					$size = to_readable_size($size);
					$cat = substr($item[6],10);
					$url=$saburl."api?mode=addurl&name=http://www.".substr($link,6)."&nzbname=".urlencode(substr($name,9))."&apikey=".$sabapikey;
					
					
					// Movies --> $_POST['type']==1||$_POST['type']==2||$_POST['type']==54||$_POST['type']==42||$_POST['type']==9||$_POST['type']==53||
					// TV  --> $_POST['type']==5||$_POST['type']==41||$_POST['type']==7||$_POST['type']==6||
					// Music --> $_POST['type']==22||$_POST['type']==47||
					if(strpos($cat, "Movies")!=false||strpos($cat, "Documentaries")!=false){
						$sabcat="movies";
					}
					elseif(strpos($cat, "TV")!=false){
						$sabcat="tv";
					}
					elseif(strpos($cat, "Music")!=false){
						$sabcat="music";
					}
					else $sabcat="";	
							
					if(!empty($sabcat)){
						$url .="&cat=".$sabcat;
					}
					
					//$popup =(print_r($id,true)."<br>".print_r($name,true));
					$nzblink = "http://www.".substr($link,6);
					if(strlen($name)!=0){
					$table .="<tr><td><p2><a href=$url; target='nothing';><img class=\"sablink\" src=\"./media/sab2_16.png\" alt=\"Download with SABnzdd+\"/></a></td>
					 <td style='width:60%';><a href=\"$nzblink\">".$name."</a></td>
					 <td>".$size."</td>
					 <td style='width:25%'>".$cat."</p2></td></tr>";			
					}
				}
				$table.= "</table></div>";
				return $table;
}	
	
function getform(){
	//($variable == X) ? "true statement" : "false statement";
	return "<form method=\"post\"><input type=\"text\" name=\"search\" id=\"search\" value=\"".$_POST['search']."\"/>
		<input type=\"radio\" name=\"site\" value=1 onclick=\"catDropDown(this.value)\"> nzb.su</input>
		<input type=\"radio\" name=\"site\" value=2 onclick=\"catDropDown(this.value)\"> NZBMatrix</input>
		<select name=\"type\" id=\"type\">
		<option value=\"\">CATEGORIES</option>
		</select>
		<input type=\"submit\" name=\"submit\" value=\"Search\" />
		</form>";
}
?>
		<html>
			<head>
				<title>Media Front Page - Search Widget</title>
				<link rel='stylesheet' type='text/css' href='css/front.css'>
				<body>
					<iframe name="nothing" height="0" width="0" style="visibility:hidden;display:none"></iframe> 
				</body>
			</head>
		</html>
