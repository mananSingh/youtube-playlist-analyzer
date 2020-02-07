<!doctype html>
<html>
<head><title>YouTube Playlist Analyzer</title>
	<meta charset="utf-8">

	<meta name="description" content="YouTube Playlist Analyzer uses YouTube Data API to analyze any youtube playlist or channel,
	and display the related information graphically. It supports 3 modes of data representations (or views): 
	Playlist View, Timeline View and Table View. Use this tool to find out things like follows:
		Total Duration of a playlist?
		Compare video durations in a playlist/channel.
		What is the trend of views, likes, dislikes or comments on videos of a channel/playlist?
		Details of each video item - title, channel title, duration, counts, publishing time,
		short description, video link.">
	<meta name="keywords" content="youtube playlist channel analyzer youtube API total duration counts statistics trends timeline history">
	<meta name="author" content="my name">		

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="c3/favicon.ico">

	<!--pace loader -->
	<script src="/c3/pace.min.js"></script>
	 <link href="/c3/pacetheme_min.css" rel="stylesheet" />
	<!-- ending pace loader-->

	<link rel="stylesheet" href="c3/mymain.css">

	<!-- JS scripting -->
	<script>
	function changeSelected(choice){
		document.getElementsByName('stringInput')[0].placeholder='Enter the YouTube '+choice.options[choice.selectedIndex].innerHTML+'here...';
	}
	function customKeyInputVisibility(choice){
		if(choice.options[choice.selectedIndex].value=='custom'){
			document.getElementsByName('customKey')[0].style.display='';
		}
		else{
			document.getElementsByName('customKey')[0].style.display='none';
		}
	}
	</script>

</head>

<body>
	<div id="container">
		<h1><span id="brand_youtube">YouTube<span>  <span id="brand_playlist">Playlist</span>  <span id="brand_analyzer">Analyzer</span></h1>
	
	<br>
	<form method="POST" action="/submit">
		<select name="inputType" onchange="changeSelected(this);">
			<option selected="selected" value="0">Playlist URL </option>
			<option value="1">Channel URL </option>
			<option value="2">Channel Username </option>			
			<option value="3">Channel Id </option>
			<option value="4">Playlist Id </option>
		</select>
		<input type="text" name="stringInput" placeholder="Enter the YouTube Playlist URL here..."size="100" />
		<br><br><br>

		Max. Video Limit: &nbsp; 
		<select name="limit">
			<option selected="selected" value="100">100</option>
			<option value="200">200</option>
			<option value="300">300</option>
			<option value="400">400</option>
			<option value="600">600</option>
			<option value="800">800</option>
			<option value="1000">1000</option>
		</select><br><br>
		Advanced (Default is recommended): &nbsp;
		<select name="keyType" onchange="customKeyInputVisibility(this);">
			<option selected="selected" value="default">Default</option>
			<option value="custom">Custom API Key</option>
		</select>
		<input type="text" name="customKey" placeholder="Enter Custom YouTube API Key here..." size="50" style="display:none"/>
		<!-- there is a minute bug in the toggle functionality; But, Not harmful for naive users. -->

		<br><br><br>
		<input type="submit" value="Submit"/>
	</form>

	<br><br><hr><br><br>
	<em>YouTube Playlist Analyzer</em> uses YouTube Data API to <b>analyze any youtube playlist or channel</b>,
	and display the related information graphically. It supports 3 modes of data representations (or <em>views</em>): 
	<em>Playlist View</em>, <em>Timeline View</em> and <em>Table View</em>.
	Use this tool to find out things like follows:
	<ul>
		<li>Total Duration of a playlist?</li>
		<li>Compare video durations in a playlist/channel.</li>
		<li>What is the trend of views, likes, dislikes or comments on videos of a channel/playlist?</li>
		<li>Details of each video item - title, channel title, duration, counts, publishing time,
		short description, video link.</li>
	</ul>
	<!--
	<br><br><br>
	<i>note</i>: the tool may not work properly, if the daily YouTube-API-quota of the developer 
	is over. In that case, either try again tommorrow, or use Advanced 'Custom API key' option.-->

	</div>
</body>
</html>
