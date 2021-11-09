<?php
/**
 * This script will explode the startpage.html and render each fragment.
 */

use App\core\Session;

$html = file_get_contents('app/views/startpage/startpage.html');
$fragments = explode("<!--===edit===-->", $html);

//Render header
echo $fragments[0];

if(Session::exists(SESSION_USERID)){
    $ID = Session::get('userID');
    echo str_replace('---ID---', $ID, $fragments[1]);
}else{
    echo $fragments[2];
}

echo $fragments[3];

if(Session::exists(SESSION_PRIVILEGE)){
    $privilege = Session::get(SESSION_PRIVILEGE);
    if($privilege == ADMIN){
        echo $fragments[4];
    }
}
//Render header

echo $fragments[5];

//Render profile card if you're logged in.
if(!is_null($currentUser)){
  $temp = str_replace('---first_name---', $currentUser['userFirstName'], $fragments[6]);
  if($currentUser['userImage'] == ''){
    $temp = str_replace('---SRC---', 'images/user.png', $temp);
  }else{
    $temp = str_replace('---SRC---', 'data:image/jpeg;base64,'.base64_encode($currentUser['userImage']), $temp);
  }
  $temp = str_replace('---last_name---', $currentUser['userLastName'], $temp);
  $temp = str_replace('---email---', $currentUser['userEmail'], $temp);
  $temp = str_replace('---visits---', $currentUser['visits'], $temp);
  $temp = str_replace('---last_online---', $currentUser['lastOnline'], $temp);
  $temp = str_replace('---ID---', $currentUser['usersID'], $temp);
  echo $temp;
}
echo $fragments[7];

//Render top courses
$index = 1;
foreach($courses as $course){
  $temp = str_replace('---ID---', $course["courseID"], $fragments[8]);
  $temp = str_replace('---PLACEMENT---', $index, $temp);
  $temp = str_replace('---name---', $course["name"], $temp);
  $temp = str_replace('---score---', $course["average_rating"], $temp);
  $temp = str_replace('---country---', $course["country"], $temp);
  $temp = str_replace('---city---', $course["city"], $temp);
  $temp = str_replace('---added---', $course["added"], $temp);
  $temp = str_replace('---university---', $course["university"], $temp);
  $temp = str_replace('---credits---', $course["credits"], $temp);
  echo $temp;
  $index++;
}

echo $fragments[9];

//Render top forums
$index = 1;
foreach($forums as $forum){
  $temp = str_replace('---ID---', $forum["forumID"], $fragments[10]);
  $temp = str_replace('---PLACEMENT---', $index, $temp);
  $temp = str_replace('---title---', $forum["title"], $temp);
  $temp = str_replace('---created---', $forum["created"], $temp);
  $temp = str_replace('---views---', $forum["views"], $temp);
  echo $temp;
  $index++;
}
echo $fragments[11];
