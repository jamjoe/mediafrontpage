<?php
function main() {
	require_once "../../config.php";
	
	$q=$_GET["q"];
	$site = $_GET["site"];
	$tablebody = "<div style=\"overflow: auto; max-height: 70%; max-width: 100%;\"><table id=\"myTable\" class=\"tablesorter\" style=\"height:70%;overflow:auto;\">
						<thead>
							<tr>
    							<th></th>
    							<th onclick=\"setTimeout('updateRows()',50);\"><a href=#>Name <img src=\"./media/arrow.png\"/></a></th>
    							<th class=\"header filesize\" onclick=\"setTimeout('updateRows()',50);\"><a href=#>Size <img src=\"./media/arrow.png\"/></a></th>
    							<th onclick=\"setTimeout('updateRows()',50);\"><a href=#>Category <img src=\"./media/arrow.png\"/></a></th>
							</tr>
						</thead>
						<tbody>";
	if ($site == 1){
		$results = nzbsu($q, $saburl,$sabapikey, $nzbsuapi, $nzbsudl);
	}
	elseif($site == 2) {
		$results = nzbmatrix($q, $nzbusername, $nzbapi,$saburl,$sabapikey);		
	}
	elseif($site == 3) {
		$tablebody = "<div style=\"overflow: auto; max-height: 70%; max-width: 100%;\"><table id=\"myTable\" class=\"tablesorter\" style=\"height:70%;overflow:auto;\">
						<thead>
							<tr>
    							<th></th>
    							<th onclick=\"setTimeout('updateRows()',50);\"><a href=#>Name <img src=\"./media/arrow.png\"/></a></th>
    							<th onclick=\"setTimeout('updateRows()',50);\"><a href=#>Rating <img src=\"./media/arrow.png\"/></a></th>
    							<th onclick=\"setTimeout('updateRows()',50);\"><a href=#>Date Released <img src=\"./media/arrow.png\"/></a></th>
							</tr>
						</thead>
						<tbody>";
		$results = imdb($q,$cp_url);		
	}
	elseif(!empty($_GET['id'])){
		if(intval($_GET['id'])!=0){
			getInfo($_GET['id'],$cp_url);
			return false;
		}
		else{
			getCP($id, $cp_url);
			return false;
		}
	}
	else{
	$_GET['type'] = $preferredCategories;
	switch ($preferredSearch){
		case '0':
			//$_GET['type'] = '';
			//$results = nzbmatrix($q, $nzbusername, $nzbapi,$saburl,$sabapikey);
			//$results .= nzbsu($q, $saburl,$sabapikey, $nzbsuapi, $nzbsudl);
			$results = "<h1>Need to choose default Site and Category</h1>";
			break;
		case '1':
			$results = nzbmatrix($q, $nzbusername, $nzbapi,$saburl,$sabapikey);
			break;
		case '2':
			$results = nzbsu($q, $saburl,$sabapikey, $nzbsuapi, $nzbsudl);
			break;
			}
	}
	echo (!empty($results))? $tablebody.$results."</tbody></table></div>" : "<h1>Nothing found!</h1>";
}

function nzbsu($q, $saburl,$sabapikey, $nzbsuapi, $nzbsudl){

	$type = (!empty($_GET['type']))?("&cat=".$_GET['type']):"";

	$search = "http://nzb.su/api?t=search&q=".urlencode($q).$type."&apikey=".$nzbsuapi."&o=json";
	$json = @file_get_contents($search);
	$content = json_decode($json, true);
	//print_r($content);

	$table = "";
	foreach($content as &$array){
		//print_r($array);
		$id = $array['guid'];
		$name = $array['name'];
		$cat = $array['category_name'];
		$size = $array['size'];
		$postdate = "<p>Date Posted: ".$array['postdate']."</p>";
		$coments = "<p>Coments: ".$array['comments']."</p>";
		$group_name = "<p> Group name: ".$array['group_name']."</p>";
		$grabs = "<p> Grabs: ".$array['grabs']."</p>";
		(!empty($array['seriesfull']))?($seriesfull=("<p>Episode info: ".$array['seriesfull']." ".$array['tvtitle']." ".$array['tvairdate']."</p>")):($seriesfull="");

		$url="http://nzb.su/getnzb/".$id.".nzb".$nzbsudl;
		$addToSab = $saburl.'api?mode=addurl&name='.urlencode($url).'&apikey='.$sabapikey;
		$nzblink = "http://nzb.su/details/".$id;
		$name = str_replace(".", "\n", $name);
		$name = str_replace(" ", "\n", $name);
		$item_desc = $postdate.$coments.$group_name.$grabs.$seriesfull;
		$item_desc = str_replace("\n", "<br>", $item_desc);

		$addToSab = addCategory($cat,$addToSab);
		if(strlen($name)!=0){
			$table .= (strpos(strtolower($cat), "xxx")===false)?printTable($name,$cat,$size,$addToSab,$nzblink,$item_desc):("");
		}
	}
	return $table;

}

function nzbmatrix($item, $nzbusername, $nzbapi,$saburl,$sabapikey) {

	$type = (!empty($_GET['type']))?("&catid=".$_GET['type']):"";

	$search = "https://api.nzbmatrix.com/v1.1/search.php?search=".urlencode($item).$type."&username=".$nzbusername."&apikey=".$nzbapi;
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
		$id = "ID: ".substr($item[0],6);
		$name = substr($item[1],9);
		$link = "http://www.".substr($item[2], 6);
		$size = 0+substr($item[3], 6);
		$size = $size;
		$cat = substr($item[6],10);
		$addToSab=$saburl."api?mode=addurl&name=http://www.".substr($link,6)."&nzbname=".urlencode($name)."&apikey=".$sabapikey;

		$indexdate 	= "Index Date: ".substr($item[4], 12);
		$group 		= "Group: ".substr($item[7],7);
		$comments 	= "Comments: ".substr($item[8],10);
		$hits 		= "Hits: ".substr($item[9],6);
		$nfo 		= "NFO: ".substr($item[10], 5);
		$weblink 	= substr($item[11], 9);
		$image 		= substr($item[13],7);
		$item_desc	= "<p>".$id."</p><p>".$group."</p><p>".$comments."</p><p>".$hits."</p><p>".$nfo."</p><p>".$indexdate."</p>";
		//$item_desc .= (substr($item[13],7)!="")?"Pic: ".$image:"";
		

		$addToSab = addCategory($cat,$addToSab);

		if(strlen($name)!=0){
			$table .= printTable($name,$cat,$size,$addToSab,$link,$item_desc,$image, $weblink);
		}
	}
	return $table;
}

function imdb($item,$cp){
	
	$api = '1b0319774deb9c07ca72ecafa8f13f8f';
	$search = 'http://api.themoviedb.org/2.1/Movie.search/en/json/'.$api.'/'.urlencode($item);
	$json = @file_get_contents($search);
	$result = json_decode($json);

	//echo "<pre>";print_r($result);echo "</pre>";

	$table = "";
	if($result[0]!="Nothing found."){
		foreach($result as $e){
				$score		= $e->score;
				$popularity	= $e->popularity;
				$translated = $e->translated;
				$adult		= $e->adult;
				$lang		= $e->language;
				$orig_name	= $e->original_name;
				$name		= $e->name;
				$alt_name	= $e->alternative_name;
				$type		= $e->type;
				$id			= $e->id;
				$imdb_id	= $e->imdb_id;
				$votes		= $e->votes;
				$rating		= $e->rating;
				$certific	= $e->certification;
				$overview	= $e->overview;
				$released	= $e->released;
				$poster_th	= $e->posters['2']->image->url;
				$poster_lg	= $e->posters['1']->image->url;
				$last_mod	= $e->last_modified_at;
				$backdrops	= $e->backdrops;
				$url 		= $e->url;
				
				$image = "<a href=".$poster_lg." class=\"highslide\" onclick=\"return hs.expand(this)\"><img style='float: left;' width='20px' src='".$poster_th."' /></a>";
				$imdb  = "<a href='http://www.imdb.com/title/".$imdb_id."' target='_blank'><img style='float: right;' width='20px' src='./media/imdb.gif' /></a>";
				
				
				$item_desc = "";
				$item_desc .= ((!empty($orig_name))&&($orig_name!='null'||$orig_name!=""))? "<p><b>Original Name</b>: ".$orig_name."</p>":"";
				$item_desc .= ((!empty($type))&&($type!='null'||$type!=""))? "<p><b>Type</b>: ".urlencode($type)."</p>":"";
				$item_desc .= "<p><b>Overview</b>: ".$overview."</p>";
				$item_desc .= (!empty($certific))? "<p><b>Rated</b>: ".$certific."</p>":"";
				//$item_desc .= (!empty($orig_name))? "<p>Original Name:".$orig_name."</p>":"";
				//$item_desc .= (!empty($orig_name))? "<p>Original Name:".$orig_name."</p>":"";
				
				$item_desc = str_replace("\"", " - ", $item_desc);		
				$item_desc = str_replace("'", "|", $item_desc);		
				
				$cp_add = $cp."movie/imdbAdd/?id=".$imdb_id;
				
				$table .= "<tr class=\"row\" style=\"height:2em;\">";
				$table .= "<td><a href='".$cp_add."' target='_blank'><img class=\"couchpotato\" height='20px' src=\"./media/couch.png\" alt=\"Add to CouchPotato Queue\"/></a></td>";
				$table .= "<td>".$image.$imdb."<a href='#' onMouseOver=\"ShowPopupBox('$item_desc');\" onMouseOut=\"HidePopupBox();\" onClick=\"getExtra('".$id."');\">$name</a></td><td style='width:10%'>$rating</td>";
				$table .= "<td style='width:20%'>$released</td></tr>";
	
			}
			return $table;
		}
	return "";
}

function getform(){
	return "<form method=\"post\"><input type=\"text\" name=\"search\" id=\"search\" value=\"".$_POST['search']."\"/>
				<input type=\"radio\" name=\"site\" value=1 onclick=\"catDropDown(this.value)\"> nzb.su</input>
				<input type=\"radio\" name=\"site\" value=2 onclick=\"catDropDown(this.value)\"> NZBMatrix</input>
				<select name=\"type\" id=\"type\">
					<option value=\"\">CATEGORIES</option>
				</select>
				<input type=\"submit\" name=\"submit\" value=\"Search\" />
			</form>";
}

function printTable($name,$cat,$size,$addToSab,$nzblink,$item_desc, $image="", $weblink="" ){
	if($image!=""){
	$image = "<a href=".$image." class=\"highslide\" onclick=\"return hs.expand(this)\"><img style='float: left;' width='20px' src='".$image."' /></a>";
	}
	if($weblink!=""){
		if(strpos($weblink,'imdb')!==false){
			$weblink = "<a href=".$weblink." target='_blank'><img style='float: right;' width='20px' src='./media/imdb.gif' /></a>";
		}
		else{
			$weblink = "";
		}
	}
	return "	<tr class=\"row\" style=\"height:3em;\">
					<td><a href=\"#\";  onclick=\"sabAddUrl('".htmlentities($addToSab)."'); return false;\"><img class=\"sablink\" src=\"./media/sab2_16.png\" alt=\"Download with SABnzdd+\"/></a></td>
					<td style='width:60%';>".$image.$weblink."<a href='".$nzblink."' target='_blank'; onMouseOver=\"ShowPopupBox('".$item_desc."');\" onMouseOut=\"HidePopupBox();\">$name</a></td>
					<td class='filesize'>".ByteSize($size)."</td>
					<td style='width:20%'>$cat</td>
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


function getInfo($id,$cp)
{
	echo "<button type='button' onclick='closeExtra();'><-Back</button>";
	echo "<div>";
	$search = "http://api.themoviedb.org/2.1/Movie.getInfo/en/json/1b0319774deb9c07ca72ecafa8f13f8f/".$id;
	$json = @file_get_contents($search);
	$result = json_decode($json);

	//echo "<pre>";print_r($result[0]);echo "</pre>";
	
	
	$m = $result[0];
	$name	  = $m->name; 
	$overview = $m->overview;
	$famous   = $m->popularity;
	$orig_name= $m->original_name;
	$rating	  = $m->rating;
	$tag	  = $m->tagline;
	$cert	  = $m->certification;
	$runtime  = $m->runtime;
	$budget	  = $m->budget;
	$revenue  = $m->revenue;
	$homepage = $m->homepage;
	$trailer  = $m->trailer;
	$votes	  = $m->votes;
	$imdb_id  = $m->imdb_id;
	$url	  = $m->url;
	$released = $m->released;	
	/*
	$	  = $m->;
	$	  = $m->;
	$	  = $m->;
	$	  = $m->;
	$	  = $m->;
*/


	$x=0;
	foreach($m->studios as $c){
		$studio_name[$x] = $c->name;
		//echo $studio_name[$x];
		$x++;
	}

	$x=0;
	foreach($m->posters as $c){
		if($c->image->height>150 && $c->image->height<250){
			$poster[$x] = $c->image->url;
			$x++;
		}
	}

	$x=0;
	foreach($m->backdrops as $c){
		if($c->image->size == 'poster'){
			$backdrop[$x] = $c->image->url;
			$x++;
		}
	}
	
	$poster[0] = (empty($poster[0]))?"./media/no_poster.png":$poster[0];

	setlocale(LC_MONETARY, 'en_US');
	$budget = money_format('%(#0n', $budget);
	$revenue = money_format('%(#0n', $revenue);


	$cp_add = $cp."movie/imdbAdd/?id=".$imdb_id;
	$homepage_tag = ((!empty($homepage))?("<a href='".$homepage."' target='_blank' >"):("<a>"));
	$trailer_tag  = ((!empty($trailer))?("<a href='".$trailer."' target='_blank' >"):("<a>"));
	
	//<a href='#' onClick=\"getExtra('".$imdb_id."');\"><img height='12px' src='./media/couch.png' style='float: left;'/></a>
	
	echo "<p><a href='$cp_add' target='_blank'><img height='12px' src='./media/couch.png' style='float: left; padding-right:4px;'/></a>";
	echo "<a href='http://www.imdb.com/title/".$imdb_id."' target='_blank'><img style='float: left; padding-right:4px;' width='18px' height='10px' src='./media/imdb.gif' /></a>";
	echo "<a href='$url' target='_blank'><img style='float: left; padding-right:4px;' height='13px' width='16px' src='./media/moviedb.png' /></a></p>";
	echo "<h1>$name <i style='float:right; size:10;'>$rating ($votes votes)</i></h1>";
	echo "<div style='float: left; width:28%;'><p><img src='".$poster[0]."' width='100%' padding-right:6px;'></p><p>$orig_name</p><p>Date Released: $released</p><p>$tag</p></div>";
	
	echo "<table width='70%'>";
	echo "<tr><td><h3>Overview</h3></td><td align='justify'>$overview</td></tr>";
	echo "<tr><td><b>Genre</b>:</td><td> ";
	$x=0;
	foreach($m->genres as $c){
		$genre_name[$x] = $c->name;
		echo $genre_name[$x]." | ";
		$x++;
	}
	echo "</td></tr>";
	
	echo "<tr><td><b> Trailer</b>:</a></b></td><td>$trailer_tag $trailer</a></td></tr>";
	echo "<tr><td><b>Homepage</b>:</td><td>$homepage_tag $homepage</a></td></tr>";
	echo "<tr><td><b>Runtime</b>:</td><td> $runtime minutes</td></tr><tr><td><b>Budget</b>:</td><td> $budget</td></tr><tr><td><b>Revenue</b>:</td><td> $revenue</td></tr>";
	echo "<tr><td><b>Rated</b>:</td><td> $cert</td></tr><tr><td><b>IMDB ID</b>:</td><td> $imdb_id</td></tr>";
	echo "<tr><td><b>Keywords</b>:</td><td> ";
	$x=0;
	foreach($m->keywords as $c){
		$keywords[$x] = $c;
		echo $keywords[$x]." | ";
		$x++;
	}
	echo "</td></tr>";


	echo "<tr><td><button type='button' onclick='toggleCast();'>Cast</button></td></tr></table>";
	echo "<div id='cast' style='display:none;'>";
	echo "<table width='100%'><tr><th></th><th> Name</th><th>Job</th><th>Character</th></tr>";
	foreach($m->cast as $c){
		$actor_name = $c->name;
		$actor_job	= $c->job;
		$actor_char	= $c->character;
		$actor_url 	= $c->url;
		$actor_thumb= $c->profile;

		$bg = ($x%2)?"style='background-color:#303030;'":"";
		$x++;
		$actor_thumb = (!empty($actor_thumb))?$actor_thumb:"./media/no_face.png";
		echo "<tr $bg><td width='25px'><img src='$actor_thumb' height='37px' width='25px'></td><td valign='middle'> $actor_name </td><td> $actor_job </td><td> $actor_char</td></tr>";
				
	}
	echo "</table>";
	echo "</div>";
	echo "<div id='addcp'></div>";
	echo "<div style='clear:both;'></div>";
	echo "</div>";
}
function getCP($id,$cp)
{

	$x = file_get_contents($cp."movie/imdbAdd/?id=".$id);
	
	echo "<pre>";print_r($x);echo "</pre>";

}

function ByteSize($bytes)
{
	$size = $bytes / 1024;
	if($size < 1024)
	{
		$size = number_format($size, 2);
		$size .= ' KB';
	}
	else
	{
		if($size / 1024 < 1024)
		{
			$size = number_format($size / 1024, 2);
			$size .= ' MB';
		}
		else if ($size / 1024 / 1024 < 1024)
			{
				$size = number_format($size / 1024 / 1024, 2);
				$size .= ' GB';
			}

		else if ($size / 1024 / 1024 / 1024 < 1024)
			{
				$size = number_format($size / 1024 / 1024/ 1024, 2);
				$size .= ' TB';
			}
	}
	return $size;
}
?>
		<html>
			<head>
				<link rel='stylesheet' type='text/css' href='css/front.css'>
			</head>
			<body>
				<iframe name="nothing" height="0" width="0" style="visibility:hidden;display:none;"></iframe>
				<?php main();?>
			</body>
		</html>
