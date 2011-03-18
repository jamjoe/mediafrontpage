<?php
function main() {
	require_once "../../config.php";

	//echo $nzbusername." ".$nzbapi." ".$saburl." ".$sabapikey;

	$q=$_GET["q"];
	$site = $_GET["site"];
	echo "<div id=\"tableResults\" style=\"height:70%;overflow:auto;\">
					<table id=\"myTable\" class=\"tablesorter\">
						<thead>
							<tr>
    							<th></th>
    							<th onclick=\"setTimeout('updateRows()',50);\"><a href=#>Name <img src=\"./media/arrow.png\"/></a></th>
    							<th onclick=\"setTimeout('updateRows()',50);\"><a href=#>Size <img src=\"./media/arrow.png\"/></a></th>
    							<th onclick=\"setTimeout('updateRows()',50);\"><a href=#>Category <img src=\"./media/arrow.png\"/></a></th>
							</tr>
						</thead>
						<tbody>";
	if ($site == 1){
		echo nzbsu($q, $saburl,$sabapikey, $nzbsuapi, $nzbsudl);
	}
	else{
		echo nzbmatrix($q, $nzbusername, $nzbapi,$saburl,$sabapikey);
	}
	echo "</div>";
}

function nzbsu($q, $saburl,$sabapikey, $nzbsuapi, $nzbsudl){

	$type = "";

	if(!empty($_POST['type'])){
		$type = "&cat=".$_POST['type'];
	}

	$table = "";
	$search = "http://nzb.su/api?t=search&q=".urlencode($q).$type."&apikey=".$nzbsuapi."&o=json";
	$json = @file_get_contents($search);
	$content = json_decode($json, true);
	//print_r($content);

	foreach($content as &$array){
		//print_r($array);
		$id = $array[guid];
		$name = $array[name];
		$cat = $array[category_name];
		$size = $array[size];
		$postdate = "<p>Date Posted: ".$array[postdate]."</p>";
		$coments = "<p>Coments: ".$array[comments]."</p>";
		$group_name = "<p> Group name: ".$array[group_name]."</p>";
		$grabs = "<p> Grabs: ".$array[grabs]."</p>";
		(!empty($array[seriesfull]))?($seriesfull=("<p>Episode info: ".$array[seriesfull]." ".$array[tvtitle]." ".$array[tvairdate]."</p>")):($seriesfull="");

		$url="http://nzb.su/getnzb/".$id.".nzb".$nzbsudl;
		$addToSab = $saburl.'api?mode=addurl&name='.urlencode($url).'&apikey='.$sabapikey;
		$nzblink = "http://nzb.su/details/".$id;
		$name = str_replace(".", "\n", $name);
		$name = str_replace(" ", "\n", $name);
		$item_desc = $postdate.$coments.$group_name.$grabs.$seriesfull;
		$item_desc = str_replace("\n", "<br>", $item_desc);

		$addToSab = addCategory($cat,$addToSab);
		if(strlen($name)!=0){
			$table .= printTable($name,$cat,$size,$addToSab,$nzblink,$item_desc);
		}
	}
	return $table;

}

function nzbmatrix($item, $nzbusername, $nzbapi,$saburl,$sabapikey) {
	$type = "";
	if(!empty($_POST['type'])){
		$type = "&catid=".$_POST['type'];
	}
	$search = "http://api.nzbmatrix.com/v1.1/search.php?search=".urlencode($item).$type."&username=".$nzbusername."&apikey=".$nzbapi;
	$content = file_get_contents($search);
	$itemArray = explode('|',$content);


	$table = "";

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
		$link = "http://www.".substr($item[2], 6);
		$size = 0+substr($item[3], 6);
		$size = $size;
		$cat = substr($item[6],10);
		$addToSab=$saburl."api?mode=addurl&name=http://www.".substr($link,6)."&nzbname=".urlencode($name)."&apikey=".$sabapikey;

		$indexdate = $item[4];
		$group = $item[7];
		$comments = $item[8];
		$hits = (string)$item[9];
		$nfo = $item[10];
		$item_desc = "Not yet implemented";

		$addToSab = addCategory($cat,$addToSab);

		if(strlen($name)!=0){
			$table .= printTable($name,$cat,$size,$addToSab,$link,$item_desc);
		}
	}
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
function printTable($name,$cat,$size,$addToSab,$nzblink,$item_desc){
	return "	<tr class=\"row\">
					<td><a href=$addToSab; target='nothing';><img class=\"sablink\" src=\"./media/sab2_16.png\" alt=\"Download with SABnzdd+\"/></a></td>
					<td style='width:60%';><a href=$nzblink target='_blank'; onMouseOver=\"ShowPopupBox('".$item_desc."');\" onMouseOut=\"HidePopupBox();\">$name</a></td>
					<td>".ByteSize($size)."</td>
					<td style='width:25%'>$cat</td>
				</tr>";
}

function addCategory($cat,$url){
	$cat = strtolower($cat);
	if(strpos($cat, "movies")!==false||strpos($cat, "documentaries")!==false){
		$cat="&cat=movies";
	}
	elseif(strpos($cat, "tv")!==false){
		$cat="&cat=tv";
	}
	elseif(strpos($cat, "audio")!==false||strpos($cat, "music")!==false){
		$cat="&cat=music";
	}
	elseif(strpos($cat, "games")!==false||strpos($cat, "console")!==false){
		$cat="&cat=games";
	}
	elseif(strpos($cat, "apps")!==false||strpos($cat, "pc")!==false){
		$cat="&cat=apps";
	}
	else $cat="";

	if(!empty($cat)){
		$url .= $cat;
	}
	return $url;
}

function ByteSize($bytes)
{
	$size = $bytes / 1024;
	if($size < 1024)
	{
		$size = number_format($size, 0);
		$size .= ' KB';
	}
	else
	{
		if($size / 1024 < 1024)
		{
			$size = number_format($size / 1024, 0);
			$size .= ' MB';
		}
		else if ($size / 1024 / 1024 < 1024)
			{
				$size = number_format($size / 1024 / 1024, 0);
				$size .= ' GB';
			}

		else if ($size / 1024 / 1024 / 1024 < 1024)
			{
				$size = number_format($size / 1024 / 1024/ 1024, 0);
				$size .= ' TB';
			}
	}
	return $size;
}

?>
		<html>
			<head>
				<link rel='stylesheet' type='text/css' href='css/front.css'>
				<script type="text/javascript" src="js/jquery.js"></script>
    			<script type="text/javascript" src="js/jquery.tablesorter.js"></script>
				<script src="http://cdn.jquerytools.org/1.2.5/full/jquery.tools.min.js"></script>
			</head>
			<body>
				<iframe name="nothing" height="0" width="0" style="visibility:hidden;display:none;"></iframe>
				<?php main();?>
			</body>
		</html>
