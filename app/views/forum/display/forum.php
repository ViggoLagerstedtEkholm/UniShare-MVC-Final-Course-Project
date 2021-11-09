<?php
/**
 * This script will explode the forum.html and render each fragment.
 */

use App\core\Session;

$html = file_get_contents('app/views/forum/display/forum.html');
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

//Display forum info
$temp = str_replace('---title---', $forum["title"], $fragments[5]);
$temp = str_replace('---created---', $forum["created"], $temp);
$temp = str_replace('---views---', $forum["views"], $temp);
$temp = str_replace('---page-count---', $number_of_pages , $temp);
$temp = str_replace('---page---', $page , $temp);
$temp = str_replace('---ID---', $forum["forumID"] , $temp);
echo str_replace('---range---', $start_page_first_result + 1 . " - " . $start_page_first_result + $results_per_page , $temp);

//For each post in this forum...
foreach($posts as $post){
  if($post["userID"] == $forum["creator"]){
    echo "Thread starter";
  }
  $temp = str_replace('---userImage---', 'data:image/jpeg;base64,'.base64_encode($post["userImage"]), $fragments[6]);
  $temp = str_replace('---userDisplayName---', $post["userDisplayName"], $temp);
  $temp = str_replace('---posted---', $post["date"], $temp);
  echo str_replace('---text---', $post["text"], $temp);
}

echo $fragments[7];

//Pagination
if($page != 1 && $number_of_pages != 0){
  $temp = str_replace('---page---', $page - 1, $fragments[8]);
  echo str_replace('---ID---', $forum["forumID"], $temp);
}

if($page != $number_of_pages && $number_of_pages != 0){
  $temp =  str_replace('---page---', $page + 1, $fragments[9]);
  echo str_replace('---ID---', $forum["forumID"], $temp);
}

$temp = str_replace('---page---', $page, $fragments[10]);
echo str_replace('---ID---', $forum["forumID"], $temp);
//Pagination
