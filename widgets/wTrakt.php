<?php
$wIndex["wTrakt"] = array("name" => "trakt.tv", "type" => "inline", "block" => "traktwrapper", "function" => "wTrakt();", "headerfunction" => "wTraktHeader();");

function wTraktHeader()
{
	echo <<< TRAKTHEADER
		<script type="text/javascript" language="javascript">
		<!--
		var movierecommendation = new ContentFlow('movie_rec');
		var movietrending = new ContentFlow('movies_trakt');
		var upcomingtv = new ContentFlow('tvtrakt');

		function getPics(id){
			$.get('widgets/wTrakt.php?type='+id, function(data){ $('#itemcontainer').html(data); alert(data); addPictures(id);});
		}
		function addPictures(id)
		{
			alert(id);
	        var ic = document.getElementById('itemcontainer');
	        var is = ic.getElementsByTagName('img');
	        for (var i=0; i< is.length; i++) 
	        {
	         	if(id === 'movie_rec'){
	            	movierecommendation.addItem(is[i], 'last');
	            }
	         	if(id === 'tvtrakt'){
	            	upcomingtv.addItem(is[i], 'last');	         	
	         	}
	         	if(id === 'movies_trakt'){
	            	movietrending.addItem(is[i], 'last');
	         	}
			}
			document.getElementById('itemcontainer').innerHTML = '';
		}

		-->
		</script>
TRAKTHEADER;

}
function wTrakt()
{
	echo "<div id='wTrakt' style='overflow:scroll;overflow-x: hidden; height:40%;'>";
	
	
	echo "<h1><a style='float:left;font-size:6px;' onmouseover=\"$(this).css({color:'orange'});\" onmouseout=\"$(this).css({color:'grey'});\" onclick=\"$('#movies_trakt').html('')\";'>Clear</a>";
	echo "<a onmouseover=\"$(this).css({color:'orange'});\" onmouseout=\"$(this).css({color:'grey'});\" onclick=\"getPics('movies_trakt');\">Trending Movies</a>";
	echo "<a onmouseover=\"$(this).css({color:'orange'});\" onmouseout=\"$(this).css({color:'grey'});\" style='float:right;font-size:6px;' onclick=\"$('#movies_trakt').toggle()\";'>Hide/Show</a></h1> ";
	echo "<div id='movies_trakt'>";
	echo "<div class=\"flow\"></div>";
	//wTraktTrendingMovies();
	echo "</div>";
	
	
	echo "<h1><a onmouseover=\"$(this).css({color:'orange'});\" onmouseout=\"$(this).css({color:'grey'});\" style='float:left;font-size:6px;' onclick=\"$('#tvtrakt').html('')\";'>Clear</a>";
	echo "<a onmouseover=\"$(this).css({color:'orange'});\" onmouseout=\"$(this).css({color:'grey'});\" onclick=\"getPics('tvtrakt');\">Upcoming TV Episodes</a>";
	echo "<a onmouseover=\"$(this).css({color:'orange'});\" onmouseout=\"$(this).css({color:'grey'});\" style='float:right;font-size:6px;' onclick=\"$('#tvtrakt').toggle()\";'>Hide/Show</a></h1>";
	echo "<div id='tvtrakt'>";
	echo "<div class=\"flow\"></div>";
	//wTraktComingShows();
	echo "</div>";
	
	
	echo "<h1><a onmouseover=\"$(this).css({color:'orange'});\" onmouseout=\"$(this).css({color:'grey'});\" style='float:left;font-size:6px;' onclick=\"$('#movie_rec').html('')\";'>Clear</a>";
	echo "<a onmouseover=\"$(this).css({color:'orange'});\" onmouseout=\"$(this).css({color:'grey'});\" onclick=\"getPics('movie_rec');\">Movie Recommendations</a>";
	echo "<a onmouseover=\"$(this).css({color:'orange'});\" onmouseout=\"$(this).css({color:'grey'});\" style='float:right;font-size:6px;' onclick=\"$('#movie_rec').toggle()\";'>Hide/Show</a></h1>";
	echo "<div id=\"movie_rec\" class=\"ContentFlow\"> 
    <div class=\"flow\"> </div>";
	//wTraktMovieRecommendations();    
	echo "</div>"; 
	
	
	
	echo "<div id=\"itemcontainer\" style=\"height: 0px; width: 0px; visibility: hidden; display:none;\"></div>";
	echo "</div><!--end wTrakt-->";

}

function traktMethods($traktApiMethods = "", $post = false, $format = "json", $debug = false) 
{
	global $trakt_api,$trakt_username,$trakt_password;
	$response = "";
	echo (empty($trakt_api))?"<h1>API not set in config.php</h1>":"";
	$format = (!empty($format))?".".$format:"";
	$trakturl = "http://api.trakt.tv/".$traktApiMethods.$format."/$trakt_api";
	$trakt_password = sha1($trakt_password);
	//echo $trakturl."<-/->user:$trakt_username/pass:$trakt_password/sha1:".sha1($trakt_password);
	
	if(!empty($traktApiMethods)) 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $trakturl);
		if($post)
		{
			if(!empty($trakt_password) && !empty($trakt_username))
			{
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_USERPWD, "$trakt_username:$trakt_password"); 
			}
			else 
			{
				echo "Username|Password not set";
				curl_close($ch);
				return false;
			}
		}
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
	}
	if($debug)
	{
		echo "URL: $trakturl";
		echo "\nUSERNAME: $trakt_username";
		echo "\nPASSWORD: $trakt_password";
		echo "<pre>";print_r($response);echo "</pre>";
		return false;
	}

	return $response;
}


function wTraktTrendingMovies()
{
	$result = traktMethods("movies/trending");
	if(!empty($result))
	{
		foreach($result as $movie)
		{
			$title  = $movie->title;
			$year  = $movie->year;
			$date = $movie->released;
			$url  = $movie->url;
			$trailer= $movie->trailer;
			$runtime= $movie->runtime;
			$tag  = $movie->tagline;
			$overview= $movie->overview;
			$cert  = $movie->certification;
			$imdb  = $movie->imdb_id;
			$tmdb  = $movie->tmdb_id;
			$poster = $movie->poster;
			$poster = $movie->images->poster;
			$fanart = $movie->images->fanart;
			$watch  = $movie->watchers;

			echo "<img class=\"item\" src=\"$poster\" />";


		}
	}
}
function wTraktMovieRecommendations()
{
	$result = traktMethods("recommendations/movies", true, "");
	if(!empty($result))
	{
		if($result->status != "failure"){
			foreach($result as $movie)
			{
				$title 	 = $movie->title;
				$year 	 = $movie->year;
				$date 	 = $movie->date;
				$url 	 = $movie->url;
				$runtime = $movie->runtime;
				$tagline = $movie->tagline;
				$overview = $movie->overview;
				$cert 	 = $movie->certification;
				$imdb_id = $movie->imdb_id;
				$tmdb_id = $movie->tmdb_id;
				$poster  = $movie->images->poster;
				$fanart  = $movie->images->fanart;
				$ratings = $movie->ratings->percentage;
				$votes	 = $movie->ratings->votes;
				$loved	 = $movie->ratings->loved;
				$hated	 = $movie->ratings->hated;
				
				echo "<img class=\"item\" src=\"$poster\" />";
			
			}
		}
		else
		{
			echo "Authentication failed";
		}
	}
}
function wTraktComingShows()
{
	$result = traktMethods("calendar/shows");

	if(!empty($result)){
		foreach($result as $item)
		{
			$date = $item->date;
			foreach($item->episodes as $episodes)
			{
				//print_r($episodes->show)
				$showTitle  = $episodes->show->title;
				$year   = $episodes->show->year;
				$showUrl  = $episodes->show->url;
				$aired   = $episodes->show->first_aired;
				$country  = $episodes->show->country;
				$showOverview  = $episodes->show->overview;
				$runtime  = $episodes->show->runtime;
				$network  = $episodes->show->network;
				$airday  = $episodes->show->air_day;
				$airtime  = $episodes->show->air_time;
				$cert  = $episodes->show->certification;
				$imdb   = $episodes->show->imdb_id;
				$tvdb   = $episodes->show->tvdb_id;
				$tvrage  = $episodes->show->tvrage_id;
				$poster  = $episodes->show->images->poster;
				$fanart  = $episodes->show->images->fanart;
				$season  = sprintf('%02d',$episodes->episode->season);
				$episode  = sprintf('%02d',$episodes->episode->number);
				$Title   = $episodes->episode->title;
				$overview  = $episodes->episode->overview;
				$url  = $episodes->episode->url;
				$firstaired = $episodes->episode->first_aired;
				$screen  = $episodes->episode->images->screen;

				echo "<img class=\"item\" src=\"$poster\" />";

			}
		}
	}
}

if(!empty($_GET['type']))
{
	require_once "../config.php";
	if($_GET['type'] == 'movies_trakt')
	{
		wTraktTrendingMovies();
	}
	if($_GET['type'] == 'tvtrakt')
	{
		wTraktComingShows();
	}
	if($_GET['type'] == 'movie_rec')
	{
		wTraktMovieRecommendations();
	}
}
?>