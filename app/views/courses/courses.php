<?php
/**
 * This script will explode the courses.html and render each fragment.
 */

use App\Core\Session;
$html = file_get_contents('app/views/courses/courses.html');
$fragments = explode("<!--===edit===-->", $html);

echo $fragments[0];

//Render header
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

//Course information
$temp = str_replace('---name---', $course["name"], $fragments[6]);
$temp = str_replace('---credits---', $course["credits"], $temp);
$temp = str_replace('---added---', $course["added"], $temp);
$temp = str_replace('---country---', $course["country"], $temp);
$temp = str_replace('---city---', $course["city"], $temp);
$temp = str_replace('---university---', $course["university"], $temp);
echo $temp;
echo $fragments[7];

$temp = str_replace('---score---', $score, $fragments[8]);
$temp = str_replace('---total_votes---', $total_votes, $temp);
$temp = str_replace('---popularity-rank---', $POPULARITY_RANK, $temp);
$temp = str_replace('---ranking-rank---', $RATING_RANK, $temp);
$temp = str_replace(' ---reviews--- ', $amountOfReviews, $temp);
echo $temp;
echo $fragments[9];

if(Session::isLoggedIn()){
  $temp = str_replace('---rating---', $rating, $fragments[10]);
  echo str_replace('---ID---', $course["courseID"], $temp);
}

echo str_replace('---text---', $course["description"], $fragments[11]);

if(Session::isLoggedIn()){
  echo str_replace('---ID---', $course["courseID"], $fragments[12]);
}
//Course information

//Filtering info
$temp = str_replace('---page-count---', $number_of_pages , $fragments[13]);
$temp = str_replace('---page---', $page , $temp);
echo str_replace('---range---', $start_page_first_result + 1 . " - " . $start_page_first_result + $results_per_page , $temp);

//Go through all reviews
foreach($reviews as $review){
  if(is_null($review["userImage"])){
    $temp = str_replace('---userImage---', 'images/user.png', $fragments[14]);
  }else{
    $temp = str_replace('---userImage---', 'data:image/jpeg;base64,'. base64_encode($review["userImage"]), $fragments[14]);
  }
  $temp = str_replace('---courseID---', $review["courseID"], $temp);
  echo str_replace('---userID---', $review["userID"], $temp);

  if(Session::isLoggedIn() && $review["userID"] == Session::get(SESSION_USERID)){
   $temp = str_replace('---overall---', $review["overall"], $fragments[15]);
   $temp = str_replace('---courseID---', $review["courseID"], $temp);
   echo str_replace('---userID---', $review["userID"], $temp);
  }

  $temp = str_replace('---userDisplayName---', $review["userDisplayName"], $fragments[16]);
  $temp = str_replace('---text---', $review["text"], $temp);
  $temp = str_replace('---fulfilling---', $review["fulfilling"], $temp);
  $temp = str_replace('---environment---', $review["environment"], $temp);
  $temp = str_replace('---difficulty---', $review["difficulty"], $temp);
  $temp = str_replace('---grading---', $review["grading"], $temp);
  $temp = str_replace('---litterature---', $review["litterature"], $temp);
  $temp = str_replace('---overall---', $review["overall"], $temp);
  echo $temp;
}

echo $fragments[17];

//Pagination
if($page != 1 && $number_of_pages != 0){
  $temp = str_replace('---page---', $page - 1, $fragments[18]);
  $temp = str_replace('---ID---', $course["courseID"], $temp);
  echo $temp;
}

if($page != $number_of_pages && $number_of_pages != 0){
  $temp = str_replace('---page---', $page + 1, $fragments[19]);
  $temp = str_replace('---ID---', $course["courseID"], $temp);
  echo $temp;
}

$temp = str_replace('---page---', $page, $fragments[20]);
$temp = str_replace('---ID---', $course["courseID"], $temp);
echo $temp;
//Pagination
