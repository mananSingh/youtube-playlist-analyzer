<!doctype html>
<head>
	<title>YouTube Playlist info.</title>
	<meta charset='utf-8'>
	
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

	<link rel="stylesheet" href="c3/mymain.css">

	<!--pace loader -->
	<script src="/c3/pace.min.js"></script>
	<link href="/c3/pacetheme_min.css" rel="stylesheet" />
	<!-- ending pace loader-->

<style>
.c3-tooltip-container{
	max-width: 200px;
}
table{border-collapse: collapse;} #tableData table, #tableData td{ border:1px solid black; padding: 4px} 
html{font-family: sans-serif;}
.imageCol {display:none;}
.publishedAtCol {display: none;}
.descriptionCol {display: none;}
.channelTitleCol {display: none;}
.countsCol {display: none;}
#info{
	margin: 0em 5em 2em 5em;
	padding: 2em;
	line-height: 150%;
}

body {
  background: #eeeded;
}

.card {
  background: #fff;
  border-radius: 2px;
}

.card-1 {
  box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
}

</style>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-51999582-13', 'auto');
  ga('send', 'pageview');

</script>
</head>

<body>
	<div id="container">

	<?php require("playlist_info_main.php"); ?>


<!-- ////////////////////////////////////////////
main html
//////////////////////////////////////////// -->

	<!-- total_duration -->
	<div id="durationContainer">
	</div>
	<br>

	<!-- choose view (let's call this 'mode') -->
	Views: &nbsp; 
	<select id="modeChoice" onchange="modeSelect();">
		<option value="0">Playlist view</option>
		<option value="1">Timeline view</option>
		<option value="2">Table view</option>
	</select>

	<hr>
	
	<!-- php require. -->
	<div id="tableContainer">
		<h2 style="display: inline">Table View &nbsp;&nbsp; </h2>
		Extra Columns: &nbsp;
		<input type="checkbox" onclick="togglePublishedAt(this);">Published At &nbsp; 
		<input type="checkbox" onclick="toggleDescription(this);">Description <span style="font-size:0.8em">(first 500 characters)</span> &nbsp;
		<input type="checkbox" onclick="toggleChannelTitle(this);">Channel Title &nbsp;
		<input type="checkbox" onclick="toggleCounts(this);">Counts &nbsp;
		<input type="checkbox" onclick="toggleImage(this);">Thumbnail Image &nbsp;

		<br><br>
		<div id="tableData"></div>
	</div>
	
	<div id="chartContainer">
	<!-- options 4 chart graph -->
	<h2 style="display: inline">Playlist View &nbsp;&nbsp; </h2>
	<select id="viewChoice" onchange="viewSelection();">
		<option value="0" selected>Durations</option>
		<option value="1">Statistics</options>
		<option value="2">Composite View</options>
	</select> 
	&nbsp; &nbsp; &nbsp; Zoom:
	<select id="zoomChoice" onchange="zoomSelect();">
		<option value="0" selected>Off</option>
		<option value="1">On</options>
	</select>
	&nbsp; &nbsp; &nbsp;<button style="float:right" onclick="resetCharts()">Reset</button>
	<br><br><div id="chart"></div>
	</div>

	<div id="timelineContainer">
		<h2 style="display: inline">Timeline View  &nbsp;&nbsp;</h2>
		 <select id="view2Choice" onchange="view2Selection();">
			<option value="0" selected>Videos</option>
			<option value="1">Durations</option>
			<option value="2">Statistics</options>
			<option value="3">Composite View</options>
		</select>
		&nbsp; &nbsp; &nbsp; Videos Represented as: 
		 <select id="videoView" onchange="videoView();">
			<option value="0" selected>Points only</option>
			<option value="1">Connected Points</options>
		</select>
		&nbsp; &nbsp; &nbsp; Zoom:
		<select id="zoom2Choice" onchange="zoom2Select();">
			<option value="0" selected>Off</option>
			<option value="1">On</option>
		</select>
		&nbsp; &nbsp; &nbsp;<button style="float:right" onclick="resetCharts()">Reset</button>
		<br><br><div id="timeline"></div>
	</div>

	<!-- selective info -->
	<br><br>
	<div id="info"></div>


<!-- Load c3.css -->
	<link href="c3/c3.min.css" rel="stylesheet"/>

	<!-- Load d3.js and c3.js -->
	<script src="c3/d3.v3.min.js" charset="utf-8"></script>
	<script src="c3/c3.min.js"></script>

<!-- ////////////////////////////////////////////
main JS
//////////////////////////////////////////// -->

<script>


var defaultMode = <?php echo $defaultMode ?>;
var json_items = <?php echo json_encode($items4json_array, JSON_PRETTY_PRINT); ?>;

document.getElementById("durationContainer").innerHTML = "Average Duration: <?php echo $duration_avg; ?> &nbsp; | &nbsp; Total Duration: <?php echo $duration_total; ?>";

////////////////////////////////
////////////////////////////////

//new code for table view

function tableCreate(){
	var output="";
	output+="<table>";
	//output+="<tr><th></th><th>Title</th><th class='imageCol'>Thumbnail</th><th class='publishedAtCol'>Date</th><th class='descriptionCol'>Description</th><th class='channelTitleCol'>Channel</th><th>Id / link</th><th >Duration</th><th class='countsCol'>Counts</th></tr>";
	for(var i=0; i<json_items.length; i++){
		output+="<tr>";
		output+="<td >"+json_items[i].count+"</td>";
		output+="<td >"+json_items[i].title+"</td>";
		output+="<td class='imageCol'><img src='"+json_items[i].thumbnailUrl+"'></td>";
		output+="<td class='publishedAtCol'>"+json_items[i].publishedAt+"</td>";
		output+="<td class='descriptionCol'>"+json_items[i].description.substring(0,500)+"</td>";
		output+="<td class='channelTitleCol'>"+json_items[i].channelTitle+"</td>";
		output+="<td ><a target='_blank' href='https://www.youtube.com/watch?v="+json_items[i].videoId+"'>"+json_items[i].videoId+"</a></td>";
		output+="<td >"+json_items[i].duration_formatted+"</td>";
		output+="<td class='countsCol'>"+json_items[i].viewCount+" views, "+json_items[i].likeCount+" likes, "+json_items[i].dislikeCount+" dislikes, "+json_items[i].commentCount+" comments.</td>";
		output+="</tr>";
	}
	output+="</table>";
	document.getElementById('tableData').innerHTML = output;
}
tableCreate();


function togglePublishedAt(cb){
	var colArray = document.getElementsByClassName('publishedAtCol');
	if(cb.checked){
		for(var i=0; i<colArray.length; i++){
			colArray[i].style.display="block";
		}
	}else{
		for(var i=0; i<colArray.length; i++){
			colArray[i].style.display="none";
		}
	}
}
function toggleDescription(cb){
	var colArray = document.getElementsByClassName('descriptionCol');
	if(cb.checked){
		for(var i=0; i<colArray.length; i++){
			colArray[i].style.display="block";
		}
	}else{
		for(var i=0; i<colArray.length; i++){
			colArray[i].style.display="none";
		}
	}
}
function toggleChannelTitle(cb){
	var colArray = document.getElementsByClassName('channelTitleCol');
	if(cb.checked){
		for(var i=0; i<colArray.length; i++){
			colArray[i].style.display="block";
		}
	}else{
		for(var i=0; i<colArray.length; i++){
			colArray[i].style.display="none";
		}
	}
}
function toggleCounts(cb){
	var colArray = document.getElementsByClassName('countsCol');
	if(cb.checked){
		for(var i=0; i<colArray.length; i++){
			colArray[i].style.display="block";
		}
	}else{
		for(var i=0; i<colArray.length; i++){
			colArray[i].style.display="none";
		}
	}
}
function toggleImage(cb){
	var colArray = document.getElementsByClassName('imageCol');
	if(cb.checked){
		for(var i=0; i<colArray.length; i++){
			colArray[i].style.display="block";
		}
	}else{
		for(var i=0; i<colArray.length; i++){
			colArray[i].style.display="none";
		}
	}
}




////////////////////////////////
////////////////////////////////
////////////////////////////////
////////////////////////////////

/*----------------------------------
	show_y2
--------------------------------- */
c3.chart.fn.axis.show_y2 = function (shown) {
    var $$ = this.internal, config = $$.config;
    config.axis_y2_show = !!shown;
    $$.axes.y2.style("visibility", config.axis_y2_show ? 'visible' : 'hidden');
    $$.redraw();
};

/*----------------------------------
	show_y
--------------------------------- */
c3.chart.fn.axis.show_y = function (shown) {
    var $$ = this.internal, config = $$.config;
    config.axis_y_show = !!shown;
    $$.axes.y.style("visibility", config.axis_y_show ? 'visible' : 'hidden');
    $$.redraw();
};



var chart = c3.generate({
    bindto: '#chart',
    data: {
    	json: json_items,
    	keys: {
    		x: 'count',	//this 'count' is the 'id' or 'serial no.' of video items
    		value: ['viewCount', 'commentCount', 'likeCount', 'dislikeCount', 'duration_in_seconds']
    	},
	hide: [ 'viewCount', 'commentCount', 'likeCount', 'dislikeCount'],
	axes:{
		duration_in_seconds: 'y2'
	},
	names: { 
		viewCount: 'Views',
		commentCount: 'Comments',
		likeCount: 'Likes',
		dislikeCount: 'Dislikes',
		duration_in_seconds: 'Duration' 
	},
	types:{
		duration_in_seconds: 'bar'
	},
	colors: { 
		duration_in_seconds: '#cecece' 
	},
		onclick: function(d, element) {
			d = d.index;	//since, order of 'this' is equal to normal index order (unlike in 'timeline view')
			renderInfo('info', d);	//info is 'id' of the div.
		},		
		selection: {
			enabled: true, grouped: true, multiple: false
		}
    },
    axis: {
    	x:{
    		//type: 'category',
		label: 'Video No.'    	
    	},
	y:{
		label: "Counts", show: false
	},
	y2:{
		label: 'Video Duration',
		show: true,
		tick: { format: function(d){return (new Date(null, null, null, null, null, d).toTimeString().match(/\d{2}:\d{2}:\d{2}/))[0]} }	
	}
    },	
	//legend: { position: 'right' }, 
	tooltip: {
		format: {
		    title: function (d) { return json_items[d-1].title; }
		}
	},
	bar:{ width: { ratio: 0.5 } }
	//write a function which adds this to 'chart' if no. of items in item_json greater than 15
	//zoom: {
	//	enabled: true,
	//	extent: [1, 10]
	//}
});



var timeline = c3.generate({
	bindto: '#timeline',
	data: {	xFormat:'%Y-%m-%d %H:%M:%S',
		json: json_items,
		keys: {
			x: 'publishedAt',
			value: ['viewCount', 'commentCount', 'likeCount', 'dislikeCount',  'duration_in_seconds']
		},
		hide: [ 'commentCount', 'dislikeCount', 'likeCount', 'duration_in_seconds'],
		axes:{
			duration_in_seconds: 'y2'
		},
		names: {
			viewCount: 'Uploaded Videos',
			commentCount: 'Comments',
			likeCount: 'Likes',
			dislikeCount: 'Dislikes',
			duration_in_seconds: 'Video Duration' 
		},
		types:{
			viewCount: 'scatter',
			duration_in_seconds: 'bar'
		},
		colors:{ 
			duration_in_seconds: '#cecece'
		},
		selection: {
			enabled: true, grouped: true, multiple: false,
			isselectable: function (d){ if(d.id=='viewCount') return true; else return false; }
		},
		onclick: function(d, element) {
			//d = d.index;	//since, videos are not represented in the normal order (but in chronological order)
			//to find proper index, d; it's essential to find the element which matches 'publishedAt' of 'this':			 
					
					var selectedItems = this.selected('viewCount');
					if(selectedItems.length){
						for(var i=0; i<selectedItems.length; i++)
								if(selectedItems[i].index !=  d.index)
									this.unselect(['viewCount']);
					}
					this.select(['viewCount'], [d.index]);
					
				var i=0; 
				//console.log(new Date(d)); console.log(new Date(json_items[0].publishedAt));
				while(i<json_items.length)
				for(var i=0; i < (json_items.length); i++){
					var a = json_items[i].publishedAt;
					var b = parseDate(d.x); 
					if( String(a) == String(b) ) { 
						d = i;
						renderInfo('info', d); 
						return;
					} //d is equal to index of 'matching publishedAt' item
				}
			}		
	},
	axis: {
		x:{	label: "Published At",
			type: 'timeseries',
			//tick: {format: function(x){return x.getFullYear();}}
			localtime: true,
			tick: {format: '%e %b %y',
				count: 7
			}		
		},
		y:{ label: "No. of Views" },
		y2:{
			label: 'Video Duration',
			show: false,		//by default, no y2.
			tick: { format: function(d){return (new Date(null, null, null, null, null, d).toTimeString().match(/\d{2}:\d{2}:\d{2}/))[0];} }	
		}
	},
	grid: {
		//x: { show: true },
		y: { show: true	}
	    },
	point: {
		r: 5,
		focus: {
		    expand: { enabled: true }
		}
	},
	bar:{ width: 10 }, 
	tooltip: {
		format: {
			title: function(d){ 
				var i=0; 
				//console.log(new Date(d)); console.log(new Date(json_items[0].publishedAt));
				while(i<json_items.length)
				for(var i=0; i < (json_items.length); i++){
					var a = json_items[i].publishedAt;
					var b = parseDate(d);
					if( String(a) == String(b) ) return (json_items[i].title)+'<br><br>'+
									d.toDateString()+'<br>'+d.toLocaleTimeString();
				}	
			},
			name: function(name, ratio, id, index){ if(name=='Uploaded Videos') return 'No. of Views'; else return name;} 
		}
	}
});

/* x-grids...
var count = 0;
while(count<json_items.length){
	var item = json_items[count];
	timeline.xgrids.add([{value: item.publishedAt, label: item.title}]);
	count++;
}*/


function parseDate(d) {		//from official date format to "yyyy-mm-dd hh:mm:ss" format (which is the default format of 'publishedAt').
  return  d.getFullYear() + "-" + ("00" + (d.getMonth() + 1)).slice(-2) + "-" + 
    ("00" + d.getDate()).slice(-2) + " " + 
    ("00" + d.getHours()).slice(-2) + ":" + 
    ("00" + d.getMinutes()).slice(-2) + ":" + 
    ("00" + d.getSeconds()).slice(-2);
}

function renderInfo (divId, d){	//d is 4 index
	var infoDiv = document.getElementById(divId);
	var output = '';
	output += '<img style="float:right" src="' + unescape(json_items[d].thumbnailUrl) +'" alt="thumbnail not found!">';
	output += '<span style="font-size:1.2em"><b>Video Title</b>: ' + unescape(json_items[d].title) + '</span><br>';
	output += '<b>Channel Title</b>: ' + unescape(json_items[d].channelTitle) + '<br>';		
	output += '<b>Duration</b>: ' + json_items[d].duration_formatted + '<br>';
	output += '<b>Counts</b>: ' + json_items[d].viewCount + ' views, ' + json_items[d].likeCount + ' likes, ' + json_items[d].dislikeCount + ' dislikes, ' + json_items[d].commentCount + ' comments.<br>';
	output += '<b>Published At</b>: ' + json_items[d].publishedAt + '<br>';
	output += '<b>Description</b>: ' + unescape(json_items[d].description) + '<br>';
	output += '<b>Video Id (Link)</b>: <a target="_blank" href="https://www.youtube.com/watch?v=' + json_items[d].videoId + '"> '+ json_items[d].videoId +'</a><br>';	
	infoDiv.innerHTML = output;
	infoDiv.className ="card card-1";
}


chart.hide('viewCount',{withLegend:true});
chart.hide('likeCount',{withLegend:true});
chart.hide('dislikeCount',{withLegend:true});
chart.hide('commentCount',{withLegend:true});

function viewSelection(){
	var e = document.getElementById("viewChoice");
	var value = e.options[e.selectedIndex].value;
	if(value==0){
		chart.axis.show_y2(true);
		chart.axis.show_y(false);
		chart.show('duration_in_seconds',{withLegend: true});
		chart.hide('viewCount',{withLegend:true});
		chart.hide('likeCount',{withLegend:true});
		chart.hide('dislikeCount',{withLegend:true});
		chart.hide('commentCount',{withLegend:true});
	}
	else if(value==1){	
		chart.axis.show_y2(false);	
		chart.axis.show_y(true);
		chart.hide('duration_in_seconds',{withLegend: true});
		chart.show('viewCount',{withLegend:true});
		chart.show('likeCount',{withLegend:true});
		chart.show('dislikeCount',{withLegend:true});
		chart.show('commentCount',{withLegend:true});
	}
	else if(value==2){
		chart.axis.show_y2(true);
		chart.axis.show_y(true);
		chart.show('duration_in_seconds',{withLegend: true});
		chart.show('viewCount',{withLegend:true});
		chart.show('likeCount',{withLegend:true});
		chart.show('dislikeCount',{withLegend:true});
		chart.show('commentCount',{withLegend:true});
	}
}


timeline.hide('commentCount', {withLegend: true});
timeline.hide('likeCount', {withLegend: true});
timeline.hide('dislikeCount', {withLegend: true});
timeline.hide('duration_in_seconds', {withLegend: true});

function view2Selection(){
	var e = document.getElementById("view2Choice");
	var value = e.options[e.selectedIndex].value;
	if(value==0){
		timeline.axis.show_y(true);
		timeline.axis.show_y2(false);
		timeline.show('viewCount', {withLegend: true});
		timeline.hide('commentCount', {withLegend: true});
		timeline.hide('likeCount', {withLegend: true});
		timeline.hide('dislikeCount', {withLegend: true});
		timeline.hide('duration_in_seconds', {withLegend: true});
	}
	else if (value==1){
		timeline.axis.show_y(true);
		timeline.axis.show_y2(true);
		timeline.show('viewCount', {withLegend: true});
		timeline.hide('commentCount', {withLegend: true});
		timeline.hide('likeCount', {withLegend: true});
		timeline.hide('dislikeCount', {withLegend: true});
		timeline.show('duration_in_seconds', {withLegend: true});
	}
	else if (value==2){
		timeline.axis.show_y(true);
		timeline.axis.show_y2(false);
		timeline.show('viewCount', {withLegend: true});
		timeline.show('commentCount', {withLegend: true});
		timeline.show('likeCount', {withLegend: true});
		timeline.show('dislikeCount', {withLegend: true});
		timeline.hide('duration_in_seconds', {withLegend: true});
	}
	else if (value==3){
		timeline.axis.show_y(true);
		timeline.axis.show_y2(true);
		timeline.show('viewCount', {withLegend: true});
		timeline.show('commentCount', {withLegend: true});
		timeline.show('likeCount', {withLegend: true});
		timeline.show('dislikeCount', {withLegend: true});
		timeline.show('duration_in_seconds', {withLegend: true});
	
	}
}

function videoView(){	
	var e = document.getElementById("videoView");
	var value = e.options[e.selectedIndex].value;
	if(value == 0){
		timeline.transform('scatter', 'viewCount');
	}
	else if (value == 1){
		timeline.transform('line', 'viewCount');
	}
}

function zoomSelect(){
	var e = document.getElementById("zoomChoice");
	var value = e.options[e.selectedIndex].value;
	if(value == 0){
		chart.zoom.enable(false);
	}
	else if(value == 1){
		chart.zoom.enable(true);
	}	
}
function zoom2Select(){
	var e = document.getElementById("zoom2Choice");
	var value = e.options[e.selectedIndex].value;
	if(value == 0){
		timeline.zoom.enable(false);
	}
	else if(value == 1){
		timeline.zoom.enable(true);
	}	

}


//show the correct mode/view as per 'defaultMode' variable
function defaultModeRender(){
	if(defaultMode==0){
		document.getElementById("chartContainer").style.display = "block";
		document.getElementById("modeChoice").value = 0;
		document.getElementById("timelineContainer").style.display = "none";
		document.getElementById("tableContainer").style.display = "none";
	}
	else if(defaultMode==1){
		document.getElementById("timelineContainer").style.display = "block";
		document.getElementById("modeChoice").value = 1;
		document.getElementById("chartContainer").style.display = "none";
		document.getElementById("tableContainer").style.display = "none";
	}
}
defaultModeRender();


function modeSelect(){
	var e = document.getElementById("modeChoice");
	var value = e.options[e.selectedIndex].value;
	if(value == 0){
		document.getElementById("timelineContainer").style.display = "none";
		document.getElementById("tableContainer").style.display = "none";
		document.getElementById("chartContainer").style.display = "block";
		document.getElementById("info").innerHTML = "";
		document.getElementById("info").className = "";
		timeline.unselect();
	}
	else if (value == 1){
		document.getElementById("chartContainer").style.display = "none";
		document.getElementById("tableContainer").style.display = "none";
		document.getElementById("timelineContainer").style.display = "block";
		document.getElementById("info").innerHTML = "";
		document.getElementById("info").className = "";
		chart.unselect();
	}
	else if (value == 2){
		document.getElementById("chartContainer").style.display = "none";
		document.getElementById("timelineContainer").style.display = "none";
		document.getElementById("tableContainer").style.display = "block";
		document.getElementById("info").innerHTML = "";
		document.getElementById("info").className = "";
		timeline.unselect();
		chart.unselect();
		//could have called 'resetCharts()'
	}
}

function resetCharts(){
	timeline.unselect();
	chart.unselect();
	document.getElementById("info").innerHTML = "";
	document.getElementById("info").className = "";
}

</script>

</body>
