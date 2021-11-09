<?php
/**
 * This script will explode the forum.html and render each fragment.
 */

use App\core\Session;

$html = file_get_contents('app/views/content/forum/forum.html');
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

/*
  Render filtering bar and total page-count / page-index / range
*/
$temp = str_replace('---page-count---', $number_of_pages , $fragments[5]);
$temp = str_replace('---page---', $page, $temp);
echo str_replace('---range---', $start_page_first_result + 1 . " - " . $start_page_first_result + $results_per_page , $temp);

/*
  Render all forum divs.
*/
foreach($forums as $forum){
  $temp = str_replace('---title---', $forum["title"], $fragments[6]);
  $temp = str_replace('---topic---', $forum["topic"], $temp);
  $temp = str_replace('---views---', $forum["views"], $temp);
  $temp = str_replace('---ID---', $forum["forumID"], $temp);

  echo str_replace('---SRC---', '/9.0/images/user.png', $temp);
}

echo $fragments[7];

//Pagination
if($page != 1 && $number_of_pages != 0){
  $temp = str_replace('---page---', $page - 1, $fragments[8]);
  $temp = str_replace('---filter_option---', $filterOption, $temp);
  $temp = str_replace('---action---', $filterOrder, $temp);
  $temp = str_replace('---search---', $search, $temp);
  $temp = str_replace('---results_per_page_count---', $results_per_page_count, $temp);

  echo $temp;
}

if($page != $number_of_pages && $number_of_pages != 0){
  $temp = str_replace('---page---', $page + 1, $fragments[9]);
  $temp = str_replace('---filter_option---', $filterOption, $temp);
  $temp = str_replace('---action---', $filterOrder, $temp);
  $temp = str_replace('---search---', $search, $temp);
  $temp = str_replace('---results_per_page_count---', $results_per_page_count, $temp);
  echo $temp;
}

$temp = str_replace('---page---', $page, $fragments[10]);
$temp = str_replace('---filter_option---', $filterOption, $temp);
$temp = str_replace('---action---', $filterOrder, $temp);
$temp = str_replace('---search---', $search, $temp);
$temp = str_replace('---results_per_page_count---', $results_per_page_count, $temp);
echo $temp;
//Pagination
