<?php
$wdgtSearch = array("name" => "NZBMatrix", "type" => "inline", "function" => "widgetSearch();", "headerfunction" => "widgetSearchHeader();");
$wIndex["wSearch"] = $wdgtSearch;

function widgetSearchHeader() {
	echo <<< SEARCHHEADER
<script type="text/javascript" language="javascript">
	<!--
		function catDropDown(str) {
			if(str==1){
				document.getElementById('type').innerHTML="<option value=\"\">ALL</option><option value=\"1000\">Console</option><option value=\"2000\">Movies</option><option value=\"3000\">Audio</option><option value=\"4000\">PC</option><option value=\"5000\">TV</option><option value=\"6000\">XXX</option><option value=\"7000\">Other</option>"
			}
			else if(str==2){
				document.getElementById('type').innerHTML="<option value=\"\">ALL</option><option value=\"1\">Movies: DVD</option><option value=\"2\">Movies: DIVX</option><option value=\"54\">Movies: BrRip</option><option value=\"42\">Movies: HD x264</option><option value=\"5\">TV: DVD</option><option value=\"6\">TV: DivX</option><option value=\"41\">TV: HD</option><option value=\"7\">TV: Sports</option><option value=\"9\">Documentaries</option><option value=\"53\">Documentaries: HD</option><option value=\"22\">MP3 Albums</option><option value=\"47\">MP3 Singles</option>"
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

		$table = "<table border='2'; style='width:100%';><tr><th></th><th>Name</th><th>Size</th><th>Category</th></tr>";
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

			$url="http://nzb.su/getnzb/".$id.".nzb".$nzbsudl;
			$url=$saburl.'api?mode=addurl&name='.urlencode($url).'&apikey='.$sabapikey;
			$nzblink = "http://nzb.su/details/".$id;
			$name = str_replace(".", "<br />", $name);

			if(strlen($name)!=0){
			$table .="<tr><td><p2><a href=$url; target='nothing';><img class=\"sablink\" src=\"../media/sab2_16.png\" alt=\"Download with SABnzdd+\"/></a></td>
					 <td style='width:60%'; white-space: normal'><a href=\"$nzblink\">".$name."</a></td>
					 <td>".$size."</td>
					 <td style='width:25%'>".$cat."</p2></td></tr>";		
			}
		}
		$table .= "</table>";
		return $table;

}


function nzbmatrix($item) {
	global $nzbusername, $nzbapi,$saburl,$sabapikey;
		$type = "";
		if(!empty($_POST['type'])){
			$type = "&catid=".$_POST['type'];
		}
		$search = "http://api.nzbmatrix.com/v1.1/search.php?search=".urlencode($item).$type."&username=".$nzbusername."&apikey=".$nzbapi;
		//$searchTerm = $item;
		$content = file_get_contents($search);
		$itemArray = explode('|',$content);
		$table = "<table border='2'><tr><th></th><th>Name</th><th>Size</th><th>Category</th></tr>";
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
					$table .="<tr><td><p2><a href=$url; target='nothing';><img class=\"sablink\" src=\"../media/sab2_16.png\" alt=\"Download with SABnzdd+\"/></a></td>
					 <td style='width:60%'; white-space: nowrap'><a href=\"$nzblink\">".$name."</a></td>
					 <td>".$size."</td>
					 <td style='width:25%'>".$cat."</p2></td></tr>";			
					}
				}
				$table.= "</table>";
				return $table;
}	
	
function getform(){
	//($variable == X) ? "true statement" : "false statement";
	return "<form method=\"post\"><input type=\"text\" name=\"search\" id=\"search\"/>
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
