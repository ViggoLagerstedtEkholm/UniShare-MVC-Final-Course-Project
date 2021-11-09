<?php
/**
 * This script will explode the people.html and render each fragment.
 */

use App\core\Session;

$html = file_get_contents('app/views/content/people/people.html');
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
  Render all user divs.
*/
foreach($users as $user){
  $temp = str_replace('---first_name---', $user["userFirstName"], $fragments[6]);
  $temp = str_replace('---last_name---', $user["userLastName"], $temp);
  $temp = str_replace('---email---', $user["userEmail"], $temp);
  $temp = str_replace('---username---', $user["userDisplayName"], $temp);
  $temp = str_replace('---last_online---', empty($user["lastOnline"]) ? "None" : $user["lastOnline"], $temp);

  if($user["userImage"] == ""){
    $temp = str_replace('---SRC---', '/9.0/images/user.png', $temp);
  }else{
    $temp = str_replace('---SRC---', 'data:image/jpeg;base64,'. base64_encode($user["userImage"]), $temp);
  }

  $temp = str_replace('---visits---', $user["visits"], $temp);
  $temp = str_replace('---ID---', $user["usersID"], $temp);

  echo $temp;
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
