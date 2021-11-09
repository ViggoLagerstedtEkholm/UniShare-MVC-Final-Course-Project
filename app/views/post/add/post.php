<?php
/**
 * This script will explode the post.html and render each fragment.
 */

use App\core\Session;

$html = file_get_contents('app/views/post/add/post.html');
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

echo str_replace('---forumID---', $forumID , $fragments[5]);