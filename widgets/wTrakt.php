<?php
$wIndex["wTrakt"] = array("name" => "trakt.tv", "type" => "inline", "function" => "wTrakt();", "headerfunction" => "wTraktHeader();");

function wTraktHeader()
{
	echo <<< TRAKTHEADER
		<script type="text/javascript" language="javascript">
		<!--
		-->
		</script>
TRAKTHEADER;

}
function wTrakt()
{
	echo "<div id='wTrakt' style='overflow:scroll;overflow-x: hidden; height:40%;'>";
	echo "<h1><a style='float:left;font-size:6px;' onclick=\"$('#movies_trakt').html('')\";'>Clear</a>";
	echo "<a onclick=\"$.get('widgets/wTrakt.php?type=movies', function(data){ $('#movies_trakt').html(data); });\">Trending Movies</a>";
	echo "<a style='float:right;font-size:6px;' onclick=\"$('#movies_trakt').toggle()\";'>Hide/Show</a></h1> ";
	echo "<div id='movies_trakt'></div>";
	echo "<h1><a style='float:left;font-size:6px;' onclick=\"$('#tvtrakt').html('')\";'>Clear</a>";
	echo "<a onclick=\"$.get('widgets/wTrakt.php?type=tv', function(data){ $('#tvtrakt').html(data); });\">Upcoming TV Episodes</a>";
	echo "<a style='float:right;font-size:6px;' onclick=\"$('#tvtrakt').toggle()\";'>Hide/Show</a></h1>";
	echo "<div id='tvtrakt'></div>";
	echo "<h1><a style='float:left;font-size:6px;' onclick=\"$('#movie_rec').html('')\";'>Clear</a>";
	echo "<a onclick=\"$.get('widgets/wTrakt.php?type=movierecommendations', function(data){ $('#movie_rec').html(data); });\">Movie Recommendations</a>";
	echo "<a style='float:right;font-size:6px;' onclick=\"$('#movie_rec').toggle()\";'>Hide/Show</a></h1>";
	echo "<div id='movie_rec'></div>";
	echo "</div>";

}

function traktMethods($traktApiMethods = "", $post = false, $format = "json", $debug = false) {
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
		echo "<div id='trend_ajax' class=\"ContentFlow\" style='height:200px; width:100%;'>
        <div class=\"loadIndicator\">
        	<div class=\"indicator\">
        	</div>
        </div>
        <div class=\"flow\">";
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

			echo "<img class=\"item\" title=\"$title\" src=\"$poster\" href=\"$url\" target=\"_blank\" />";


		}
		echo "</div>
       		<div class=\"globalCaption\">
       		</div>
    		</div>";
	}
}
function wTraktMovieRecommendations(){
	$result = traktMethods("recommendations/movies", true, "");
	if(!empty($result))
	{
		echo "<div id='reccomend_ajax' class=\"ContentFlow\" style='height:200px; width:100%;'>
        <div class=\"loadIndicator\">
        	<div class=\"indicator\">
        	</div>
        </div>
        <div class=\"flow\">";
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
			
			echo "<img class=\"item\" title=\"$title\" src=\"$poster\" href=\"$url\" target=\"_blank\" />";		
		
		}
		echo "</div>
       		<div class=\"globalCaption\">
       		</div>
    		</div>";
	}
}
function wTraktComingShows()
{
	$result = traktMethods("calendar/shows");

	if(!empty($result)){
		echo "<div id='tv_ajax' class=\"ContentFlow\" style='height:200; width:100%;'>
        <div class=\"loadIndicator\">
        	<div class=\"indicator\">
        	</div>
        </div>
        <div class=\"flow\">";

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
/*
				echo "<a onclick=\"toggleTrakt('$url');\">v</a><h1><div style='text-align:left; text-overflow:ellipsis; overflow:hidden; white-space: nowrap;'><a href='$showUrl' target='_blank'>$showTitle</a> ($year) </div></h1>";
				echo "<div id='$url' style='height:60%; width:100%; display:none;'>";
				echo "<div id='content'>";
				echo "<p><img src='$poster' width='20%' style='float:left;padding-bottom:5px;' /><font color='white'><p> $network  </p><p> $airday's @ $airtime for $runtime minutes </p><p><a href='$url' target='_blank'>S$season"."E$episode - $title<a>  </p><p> $overview </p></font></p>";
				echo "</div>";
				echo "</div>";
*/
				echo "<img class=\"item\" title=\"$showTitle - S$season"."E$episode\" src=\"$poster\" href=\"$showUrl\" target=\"_blank\" />";

			}
		}
		echo "</div>
       		<div class=\"globalCaption\">
       		</div>
    		</div>";
	}
	echo "<div id='clear-float'></div>";
	echo "</div>";
}
if(!empty($_GET['type']))
require_once "../config.php";
{
	if($_GET['type'] == 'movies')
	{
		wTraktTrendingMovies();
	}
	if($_GET['type'] == 'tv')
	{
		wTraktComingShows();
	}
	if($_GET['type'] == 'movierecommendations')
	{
		wTraktMovieRecommendations();
	}
}
?>
