<?php
/**
 * This script will explode the courses.html and render each fragment.
 */

use App\Core\Session;
$html = file_get_contents('app/views/content/courses/courses.html');
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
$temp = str_replace('---page---', $page , $temp);
echo str_replace('---range---', $start_page_first_result + 1 . " - " . $start_page_first_result + $results_per_page , $temp);

/*
  Render all course divs.
*/
foreach($courses as $course){
  $temp = str_replace('---name---', $course->name , $fragments[6]);
  $temp = str_replace('---credits---', $course->credits , $temp);
  $temp = str_replace('---score---', $course->rating, $temp);
  $temp = str_replace('---university---', $course->university, $temp);
  $temp = str_replace('---country---', $course->country, $temp);
  $temp = str_replace('---city---', $course->city, $temp);
  $temp = str_replace('---added---', $course->added, $temp);
  $temp = str_replace('---score---', 0.0, $temp);
  echo str_replace('---courses---', 0, $temp);

  //Display logged in features if the user is logged in!
  if(Session::isLoggedIn()){
    if($course->existsInActiveDegree){
      $temp = str_replace('---ADD_REMOVE---', "REMOVE from degree", $fragments[7]);
      $temp = str_replace('---CSS---', "button-style-2", $temp);

      echo str_replace('---ID---', $course->ID, $temp);
    }else{
      $temp = str_replace('---ADD_REMOVE---', "ADD to degree", $fragments[7]);
      $temp = str_replace('---CSS---', "button-style-3", $temp);

      echo str_replace('---ID---', $course->ID, $temp);
    }
  }

  echo str_replace('---ID---', $course->ID, $fragments[8]);
}

echo $fragments[9];

//Pagination
if($page != 1 && $number_of_pages != 0){
  $temp = str_replace('---page---', $page - 1, $fragments[10]);
  $temp = str_replace('---filter_option---', $filterOption, $temp);
  $temp = str_replace('---action---', $filterOrder, $temp);
  $temp = str_replace('---search---', $search, $temp);
  $temp = str_replace('---results_per_page_count---', $results_per_page_count, $temp);
  echo $temp;
}

if($page != $number_of_pages && $number_of_pages != 0){
  $temp = str_replace('---page---', $page + 1, $fragments[11]);
  $temp = str_replace('---filter_option---', $filterOption, $temp);
  $temp = str_replace('---action---', $filterOrder, $temp);
  $temp = str_replace('---search---', $search, $temp);
  $temp = str_replace('---results_per_page_count---', $results_per_page_count, $temp);
  echo $temp;
}

$temp = str_replace('---page---', $page, $fragments[12]);
$temp = str_replace('---filter_option---', $filterOption, $temp);
$temp = str_replace('---action---', $filterOrder, $temp);
$temp = str_replace('---search---', $search, $temp);
$temp = str_replace('---results_per_page_count---', $results_per_page_count, $temp);
echo $temp;
//Pagination
