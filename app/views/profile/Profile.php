<?php
/**
 * This script will explode the profile.html and render each fragment.
 */

use App\Core\Session;
$html = file_get_contents('app/views/profile/profile.html');
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

$sessionID = false;
if(Session::isLoggedIn()){
  $sessionID = Session::get('userID');
}

if($image == ""){
    echo str_replace('---SRC---', "images/user.png", $fragments[5]);
  }else{
    echo str_replace('---SRC---', 'data:image/jpeg;base64,'. $image, $fragments[5]);
}

//Render profile "upload image" if it's your profile.
if($sessionID != false){
  if($currentPageID == $sessionID){
    echo $fragments[6];
  }
}

//Show user info.
$info_panel = str_replace('---First_name---', $first_name, $fragments[7]);
$info_panel = str_replace('---display_name---', $display_name, $info_panel);
$info_panel = str_replace('---Last_name---', $last_name, $info_panel);
$info_panel = str_replace('---Date---', $visitDate, $info_panel);
$info_panel = str_replace('---joined---', $joined, $info_panel);
$info_panel = str_replace('---Visits---', $updatedVisitCount , $info_panel);
$info_panel = str_replace('---Added_projects---', count($projects) , $info_panel);
$info_panel =  str_replace('---Completed_courses---', 0 , $info_panel);
$info_panel =  str_replace('---privilege---', $privilege , $info_panel);
$info_panel =  str_replace('---description---', $description , $info_panel);


echo $info_panel;

//If it's your profile...
if($sessionID != false){
  if($currentPageID == $sessionID){
    echo $fragments[8];
  }
}

echo $fragments[9];

//Render all projects.
foreach ($projects as $project) {
  $project_panel = str_replace('---name---', $project["name"], $fragments[10]);
  $project_panel = str_replace('---ID---', $project["projectID"], $project_panel);
  $project_panel = str_replace('---PROJECT-SRC---', 'data:image/jpeg;base64,'.base64_encode($project["image"]), $project_panel);
  $project_panel = str_replace('---LINK---', $project["link"], $project_panel);
  $project_panel = str_replace('---description---', $project["description"], $project_panel);
  $project_panel = str_replace('---date---', $project["added"], $project_panel);

  echo $project_panel;

  //If it's your profile...
  if($sessionID != false){
    if($currentPageID == $sessionID){
      echo str_replace('---ID---', $project["projectID"], $fragments[11]);
    }
  }
  echo $fragments[12];
}

echo $fragments[13];

if($sessionID != false){
  if($currentPageID == $sessionID){
    echo $fragments[14];
  }
}

echo $fragments[15];

//Render all degrees.
foreach($degrees as $degree){
  echo str_replace('---degreeID---', $degree->ID, $fragments[16]);

  $temp = str_replace('---degree_name---', $degree->name, $fragments[17]);
  if($degree->isActiveDegree){
    $temp = str_replace('---active---', 'Active', $temp);
  }else{
    $temp = str_replace('---active---', 'Inactive', $temp);
  }
  $temp = str_replace('---degreeID---', $degree->ID, $temp);
  $temp = str_replace('---school---', $degree->university, $temp);
  $temp = str_replace('---country---', $degree->country, $temp);
  $temp = str_replace('---totalCredits---', $degree->totalCredits, $temp);
  echo str_replace('---city---', $degree->city, $temp);

  if($sessionID == $currentPageID){
    echo str_replace('---degreeID---', $degree->ID, $fragments[18]);
  }

  echo $fragments[19];

  $courses = $degree->courses;
  //Render all courses in degree.
  foreach($courses as $course){
    $temp = str_replace('---name---', $course["name"], $fragments[20]);
    $temp = str_replace('---credits---',  $course["credits"], $temp);
    $temp = str_replace('---ID---',  $course["courseID"], $temp);
    echo str_replace('---university---',  $course["university"], $temp);

    if($sessionID == $currentPageID){
      $temp = str_replace('---DEGREE-ID---', $degree->ID, $fragments[21]);
      echo str_replace('---ID---',  $course["courseID"], $temp);
    }
    echo $fragments[22];
  }
  echo $fragments[23];
}

//Render pagination info.
$temp = str_replace('---page-count---', $number_of_pages , $fragments[24]);
$temp = str_replace('---page---', $page , $temp);
$temp = str_replace('---range---', $start_page_first_result + 1 . " - " . $start_page_first_result + $results_per_page , $temp);
echo str_replace('---ID---', $currentPageID , $temp);

//Render all comments.
foreach($comments as $comment){
  $temp = str_replace('---ID---', $comment["commentID"], $fragments[25]);
  $temp = str_replace('---SRC---', 'data:image/jpeg;base64,' . base64_encode($comment["userImage"]), $temp);
  $temp = str_replace('---DISPLAY_NAME---', $comment["userDisplayName"], $temp);
  $temp = str_replace('---text---', $comment["text"], $temp);
  $temp = str_replace('---added---', $comment["date"], $temp);

  echo $temp;
  if(Session::isLoggedIn()){
    if($currentPageID == $sessionID || $comment["author"] == $sessionID){
      echo str_replace('---ID---', $comment["commentID"], $fragments[26]);
    }
  }
  echo $fragments[27];
}

echo $fragments[28];

//Pagination
if($page != 1 && $number_of_pages != 0){
  $temp = str_replace('---page---', $page - 1, $fragments[29]);
  $temp = str_replace('---ID---', $currentPageID, $temp);
  echo $temp;
}

if($page != $number_of_pages && $number_of_pages != 0){
  $temp = str_replace('---page---', $page + 1, $fragments[30]);
  $temp = str_replace('---ID---', $currentPageID, $temp);
  echo $temp;
}

$temp = str_replace('---page---', $page, $fragments[31]);
$temp = str_replace('---ID---', $currentPageID, $temp);
echo $temp;
//Pagination

