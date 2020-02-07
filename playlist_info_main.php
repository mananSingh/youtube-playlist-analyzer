<?php
//limitations of script: 1. for each, video in the playlist, duration maximum: few hours. (not more than a day; otherwise miscalculations!)
//2. slow if playlist is very big (like 400 videos.) eg. in "channel uploads playlist"; that's why user is asked for "max video limit".

$error_internet = "<br><br><span style='color:red'>Error: No Internet Connection or Bad Request</span><br><br>";
$error_invalidInput = "<br><br><span style='color:red'>Error: Invalid Input</span><br><br>";


if($_POST['keyType']=='default'){
	$myKey = "AIzaSyCyxbgBwvvjJ4kdjBLsJfSXuCg0eNPDi20";		//Put your Google/Youtube Developer API key here. (This is just a dummy one.)
}
elseif($_POST['keyType']=='custom'){
	$myKey = $_POST['customKey'];
}
else{
	echo $error_invalidInput; exit();
}


function playlist_id_from_url($url) {
	$url = parse_url($url);
	parse_str($url['query'],$q);
	$list = $q['list'];
	return $list;
}

function playlist_id_from_channelId($channelId){
	$api_request = "https://www.googleapis.com/youtube/v3/channels/?part=snippet,statistics,contentDetails&id=".$channelId."&key=".$GLOBALS['myKey'];
	$channelInfo_json = @file_get_contents($api_request);
	if($channelInfo_json==FALSE){
		echo $GLOBALS['error_internet'];
		exit();
	}
	//channel data to:  display basic channel info; & return uploads playlist id.
	$channel_data = json_decode($channelInfo_json, true);	//channel_data becomes an associative array due to 'true'

	//check whether the json data items[] is empty.
	if(!$channel_data['items']){	
		echo $GLOBALS['error_invalidInput'];
		exit();
	}
	foreach($channel_data['items'] as $items){
		$channelId = $items['id'];
		$channelTitle = $items['snippet']['title'];
		//$channelDescription = $items['snippet']['description'];
		//$channelThumbnailUrl = $items['snippet']['thumbnails']['default']['url'];
		$uploadsPlaylist = $items['contentDetails']['relatedPlaylists']['uploads'];
		$channelViewCount = $items['statistics']['viewCount'];
		//$channelCommentCount = $items['statistics']['commentCount'];
		$channelSubscriberCount = $items['statistics']['subscriberCount'];
		$channelVideoCount = $items['statistics']['videoCount'];
	}
	echo '<h2>'.$channelTitle.'</h2>';
	echo '<b>Channel ID</b>: '.$channelId;
	echo '&nbsp;|&nbsp;<b>Videos</b>: '.$channelVideoCount;
	echo '&nbsp;|&nbsp;<b>Channel Views</b>: '.$channelViewCount;
	echo '&nbsp;|&nbsp;<b>Subscribers</b>: '.$channelSubscriberCount;
	//echo '<br><b>Comments</b>: '.$channelCommentCount;
	echo '&nbsp;|&nbsp;Uploads Playlist ID: '.$uploadsPlaylist;
	return $uploadsPlaylist;
}

function playlist_id_from_user($username){
	$api_request = "https://www.googleapis.com/youtube/v3/channels/?part=snippet,statistics,contentDetails&forUsername=".$username."&key=".$GLOBALS['myKey'];
	$channelInfo_json = @file_get_contents($api_request);
	if($channelInfo_json==FALSE){
		echo $GLOBALS['error_internet'];
		exit();
	}
	//channel data to: display basic channel info; & return uploads playlist id.
	$channel_data = json_decode($channelInfo_json, true);	//channel_data becomes an associative array due to 'true'

	//check whether the json data items[] is empty.
	if(!$channel_data['items']){
		echo $GLOBALS['error_invalidInput'];
		exit();
	}
	foreach($channel_data['items'] as $items){
		$channelId = $items['id'];
		$channelTitle = $items['snippet']['title'];
		//$channelDescription = $items['snippet']['description'];
		//$channelThumbnailUrl = $items['snippet']['thumbnails']['default']['url'];
		$uploadsPlaylist = $items['contentDetails']['relatedPlaylists']['uploads'];
		$channelViewCount = $items['statistics']['viewCount'];
		//$channelCommentCount = $items['statistics']['commentCount'];
		$channelSubscriberCount = $items['statistics']['subscriberCount'];
		$channelVideoCount = $items['statistics']['videoCount'];
	}
	echo '<h2>'.$channelTitle.'</h2>';
	echo '<b>Channel ID</b>: '.$channelId;
	echo '&nbsp;|&nbsp;<b>Videos</b>: '.$channelVideoCount;
	echo '&nbsp;|&nbsp;<b>Channel Views</b>: '.$channelViewCount;
	echo '&nbsp;|&nbsp;<b>Subscribers</b>: '.$channelSubscriberCount;
	//echo '<br><b>Comments</b>: '.$channelCommentCount;
	echo '&nbsp;|&nbsp;Uploads Playlist ID: '.$uploadsPlaylist;
	return $uploadsPlaylist;
}

function display_playlistInfo($playlistId_input){
	$api_request = "https://www.googleapis.com/youtube/v3/playlists/?part=snippet,contentDetails&id=".$playlistId_input."&key=".$GLOBALS['myKey'];
	$playlistInfo_json = @file_get_contents($api_request);
	if($playlistInfo_json==FALSE){
		echo $GLOBALS['error_internet'];
		exit();
	}
	//channel data to: display basic channel info; & return uploads playlist id.
	$playlist_data = json_decode($playlistInfo_json, true);	//channel_data becomes an associative array due to 'true'

	//check whether the json data items[] is empty.
	if(!$playlist_data['items']){
		echo $GLOBALS['error_invalidInput'];
		exit();
	}
	foreach($playlist_data['items'] as $items){
		$playlistTitle = $items['snippet']['title'];
		$playlistVideoCount = $items['contentDetails']['itemCount'];
	}
	echo '<h4>'.$playlistTitle.'<span style="font-weight:normal"> &nbsp;|&nbsp; ';
	//if no. of videos in playlist more than the max. video limit.
	if($playlistVideoCount>intval($_POST['limit'])){
		echo intval($_POST['limit']);
	}else{
		echo $playlistVideoCount;
	}
	echo ' out of '.$playlistVideoCount.' videos will be analyzed.</span></h4>';
}

function secondsToTime($seconds) {
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
}

function time2seconds($str_time){
	sscanf($str_time, "%d:%d:%d:", $hours, $minutes, $seconds);
	$time_seconds = isset($seconds)?$hours*3600+$minutes*60+$seconds:$hours*60+$minutes;
	return $time_seconds;
}
function covtime($youtube_time) {
	$date = new DateTime('1970-01-01');
	$date->add(new DateInterval($youtube_time));
	return $date->format('H:i:s');
}


//based on <select> choice, compute:
//$playlistId_input =  ?  (eg: "PLB7540DEDD482705B"- ocw mit edu math4cs 6.042J)
switch(intval($_POST['inputType'])){
	case 0: //in this case, input is "playlist url"
			$playlistId_input = playlist_id_from_url($_POST['stringInput']);
			break;
	case 1: //in this case, input is channel url
			//parsing channel url for 'path' portion.
			$channelUrl = $_POST['stringInput'];
			$channelUrl = parse_url($channelUrl);
			parse_str($channelUrl['path'], $p);
			$p = key($p);
			$path_array = explode('/', $p);
			//determining whether the url contains channel id or username
			if($path_array[1]=="user"){
				//$path_array[2] will be like 'MIT', 'CSFUNTV', etc.
				$playlistId_input = playlist_id_from_user($path_array[2]);
			}
			elseif($path_array[1]=="channel"){
				//$path_array[2] will be like 'UC_T2CScXLowsKEwc1q3VdEg', etc.
				$playlistId_input = playlist_id_from_channelId($path_array[2]);
			}
			else{
				echo $GLOBALS['error_invalidInput']; exit();
			}
			break;
	case 2: //in this case, input is channel username 
			$playlistId_input = playlist_id_from_user($_POST['stringInput']);
			break;
	case 3: //in this case, input is channel id 
			$playlistId_input = playlist_id_from_channelId($_POST['stringInput']);
			break;
	case 4: //in this case, input is playlist id
			$playlistId_input = $_POST['stringInput'];
			break;
	default: echo $GLOBALS['error_invalidInput']; exit();
}

display_playlistInfo($playlistId_input);

//default mode for view. ie. table, playlist or timeline
switch(intval($_POST['inputType'])){
	case 0: $defaultMode = 0; break;
	case 1:
	case 2:
	case 3:	$defaultMode = 1; break;
	case 4: $defaultMode = 0; break;
}


$duration_total = 0;
$count = 0;
$nextPageToken = null;

$items4json_array = array();


////step 1: make the youtube api call based on the playlist-id:
nextPageLabel4goto:
if($nextPageToken==null){		//first case (or when no next page i.e. less than 50 videos in playlist
	$api_request = "https://www.googleapis.com/youtube/v3/playlistItems/?part=snippet&maxResults=50&playlistId=".$playlistId_input."&key=".$myKey;
}else{
	$api_request = "https://www.googleapis.com/youtube/v3/playlistItems/?part=snippet&maxResults=50&pageToken=".$nextPageToken."&playlistId=".$playlistId_input."&key=".$myKey;
}
$playlist_json = @file_get_contents($api_request);
if($playlist_json==FALSE){
	echo $GLOBALS['error_internet'];
	exit();
}
$playlist_data = json_decode($playlist_json, true);	//playlist_data becomes an associative array due to 'true'

//check whether the json data items[] is empty.
if(!$playlist_data['items']){
	echo $GLOBALS['error_invalidInput'];
	exit();
}


////step 2: for each item, do the next steps

foreach($playlist_data['items'] as $item_in_list){
	////step 3: extract the video link. + extract info: title, description, publishedAt, channelTitle, videoId.

	$title = $item_in_list['snippet']['title'];
	$description = $item_in_list['snippet']['description'];
	$thumbnailUrl = $item_in_list['snippet']['thumbnails']['default']['url'];

	$videoId = $item_in_list['snippet']['resourceId']['videoId'];

	////step 4: make another call to youtube api from that video link
	$api_request_4video = "https://www.googleapis.com/youtube/v3/videos/?part=snippet,contentDetails,statistics&id=".$videoId."&key=".$myKey;
	$video_json = @file_get_contents($api_request_4video);
	if($video_json==FALSE){
		echo $GLOBALS['error_internet'];
		exit();
	}

	$video_data = json_decode($video_json, true);	//video_data becomes an associative array.

	//check whether the json data items[] is empty.
	if(!$video_data['items']){
		echo $GLOBALS['error_invalidInput'];
		exit();
	}

	////step 5: extract info about: duration, counts.
	foreach($video_data['items'] as $vid_info){
		$publishedAt = strtotime($vid_info['snippet']['publishedAt']);
		$channelTitle = $vid_info['snippet']['channelTitle'];
		$duration = $vid_info['contentDetails']['duration'];
		
		$duration_formatted = covtime($duration);
		$duration_in_seconds = time2seconds($duration_formatted);
		$duration_total += $duration_in_seconds;

		$viewCount = $vid_info['statistics']['viewCount'];
		$likeCount = $vid_info['statistics']['likeCount'];
		$dislikeCount = $vid_info['statistics']['dislikeCount'];
		$commentCount = $vid_info['statistics']['commentCount'];
	}

	$count++;

/* Initial plan was:
//Entry in one row. (for each video item in the playlist.)
//	echo '<tr>';
//	echo '<td>'.$count.'</td>';
//	echo '<td>'.$title.'</td>';
//	if(isset($_POST['thumbnail'])){
//		echo '<td><img src="'.$thumbnailUrl.'"></td>';
//	}
//	if(isset($_POST['datetime'])){
//		echo '<td>'.date("Y-m-d H:i:s",$publishedAt).'</td>';
//	}
//	if(isset($_POST['description'])){
//		echo '<td>'.$description.'</td>';
//	}
//	if(isset($_POST['channel'])){
//		echo '<td>'.$channelTitle.'</td>';
//	}
//	echo '<td><a target="_blank" href="https://www.youtube.com/watch?v='.$videoId.'">'.$videoId.'</a></td>';
//	echo '<td>'.$duration_formatted.' or '.$duration_in_seconds.' s</td>';
//	if(isset($_POST['count'])){
//		echo '<td>Counts: '.$viewCount.' views, '.$likeCount.' likes, '.$dislikeCount.' dislikes, '.$commentCount.' comments.</td></tr>';
//	}*/


//Storing all the above variables in array of arrays. will be used as: array to json to
// use-in-visualization.	
$singleItem_array = array("count"=>$count,"title"=>$title,"thumbnailUrl"=>$thumbnailUrl, 
	"publishedAt"=>date("Y-m-d H:i:s",$publishedAt), "description"=>$description, "channelTitle"=>$channelTitle,
	"videoId"=>$videoId, "duration_formatted"=>$duration_formatted, 
	"duration_in_seconds"=>$duration_in_seconds, "viewCount"=>$viewCount, 
	"likeCount"=>$likeCount, "dislikeCount"=>$dislikeCount, "commentCount"=>$commentCount);

	array_push($items4json_array, $singleItem_array);
}


//i think 1000, is more than enough.otherwise, youtube data api will refuse to work (quota-limit) & other issues...

//in case, someone does a manual POST of limit>1000
if(intval($_POST['limit'])>1000){
	echo '<tr><td colspan="100%" style="color:red">Limit of Max. Videos i.e. 1000 reached.';
}
//SECRET HACK, if you want to bypass 1000 limit.
elseif($_POST['limit']=='unlimited'){
	//do nothing, but continue pagination.
}
//normal limit test.
elseif($count>=intval($_POST['limit'])){
	echo '<em>note</em>: Selected Limit of Max. Videos was: '.$_POST['limit'].'<br>';
	goto end;
}

////step 1.5: pagination?!
if(array_key_exists('nextPageToken',$playlist_data)){
	$nextPageToken=$playlist_data['nextPageToken'];
	goto nextPageLabel4goto;
}

end:


$duration_avg = intval($duration_total / $count);
$duration_avg = secondsToTime($duration_avg);
$duration_total = secondsToTime($duration_total);

//testing $items4json_array
//print_r($items4json_array);	//this variable stores items in array format for json parsing in js. 4 visualization.
?>
