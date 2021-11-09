<?php
/**
 * This script will explode the admin.html and render each fragment.
 */

use App\core\Session;

$html = file_get_contents('app/views/admin/admin.html');
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

//Render requests
echo $fragments[5];
foreach($requests as $request){
  $temp = str_replace('---name---', $request["name"], $fragments[6]);
  $temp = str_replace('---ID---', $request["requestID"], $temp);
  $temp = str_replace('---credits---', $request["credits"], $temp);
  $temp = str_replace('---university---', $request["university"], $temp);
  $temp = str_replace('---country---', $request["country"], $temp);
  $temp = str_replace('---city---', $request["city"], $temp);
  echo $temp;
}
echo $fragments[7];
//Render requests
