<?php
namespace App\Controllers;
use App\Middleware\AuthenticationMiddleware;
use App\Models\MVCModels\Courses;
use App\Core\Session;
use App\Core\Request;
use App\Core\Application;
use App\Includes\Validate;

class CourseController extends Controller{
  function __construct(){
    $this->setMiddlewares(new AuthenticationMiddleware(['setRate', 'getRate']));
    $this->courses = new Courses();
  }

  public function view(){
    if(isset($_GET["ID"])){
      $ID = $_GET["ID"];
      $course = $this->courses->getCourse($ID);
      $result = $this->courses->getArthimetricMeanScore($ID);

      $POPULARITY_RANK = $this->courses->getPopularityRank($ID)->fetch_assoc()["POPULARITY_RANK"];
      $RATING_RANK = $this->courses->getOverallRankingRating($ID)->fetch_assoc()["RATING_RANK"];

      $arthimetricMean = $result["AVG(rating)"];
      $COUNT = $result["COUNT(rating)"];

      $userRating = null;
      if(Session::isLoggedIn()){
        $userRating = $this->courses->getRate(Session::get(SESSION_USERID), $ID);
      }

      $params = [
        "rating" => $userRating,
        "course" => $course,
        "score" => $arthimetricMean,
        "total_votes" => $COUNT,
        "POPULARITY_RANK" => $POPULARITY_RANK,
        "RATING_RANK" => $RATING_RANK
      ];

      return $this->display('courses','courses', $params);
    }
    Application::$app->redirect('./');
  }

  public function setRate(Request $request){
    $ratingRequest = $request->getBody();
    $courseID = $ratingRequest["courseID"];
    $rating = $ratingRequest["rating"];

    $this->courses->setRate(Session::get(SESSION_USERID), $courseID, $rating);
  }

  public function getRate(Request $request){
    $ratingRequest = $request->getBody();
    $courseID = $ratingRequest["courseID"];
    $rating = $this->courses->getRate(Session::get(SESSION_USERID), $courseID);
    $resp = ['success'=>true,'data'=>['rating'=>$rating]];
    return $this->jsonResponse($resp);
  }

  public function review(){
    return $this->display('courses','review', []);
  }

  public function uploadReview(Request $request){
    //TODO
  }
}