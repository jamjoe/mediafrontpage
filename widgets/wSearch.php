<?php
$wdgtSearch = array("name" => "Search Widget", "type" => "inline", "function" => "widgetSearch();");
$wIndex["wSearch"] = $wdgtSearch;

function widgetSearch() {	
	 echo <<<BODY
            <div><input type="text"
                  value=""
                  id="searchterm"
                  onkeydown="if (event.keyCode == 13) document.getElementById('searchbutton').click()">  <input type="radio"
                  name="site"
                  value="1"
                  onclick="catDropDown(this.value)"> nzb.su <input type="radio"
                  name="site"
                  value="2"
                  onclick="catDropDown(this.value)"> NZBMatrix <select name="type" style="visibility:hidden;"
                  id="type">
            </select> <input type="button"
                  id="searchbutton"
                  value="Search"
                  onclick="results()"> <input type="button"
                  id="clearbutton"
                  value="Clear"
                  onclick="clearResults()"></div>
        <div id="resultstable"></div>
BODY;
}

?>
		<html>
			<head>
				<title>Media Front Page - Search Widget</title>
				<link rel='stylesheet' type='text/css' href='css/front.css'>
		        <script type="text/javascript" src="widgets/searchComponents/searchWidget.js"></script>
			</head>
			<body>
				<iframe name="nothing" height="0" width="0" style="visibility:hidden;display:none"></iframe>
			</body>
		</html>
