<?php
namespace App\Controllers;
use App\Middleware\AuthenticationMiddleware;
use App\Models\MVCModels\Reviews;
use App\Core\Session;
use App\Core\Request;
use App\Core\Application;
use App\Includes\Validate;

/**
 * Review controller for handling reviews.
 * @author Viggo Lagestedt Ekholm
 */
class ReviewController extends Controller{
  private $reviews;

  function __construct(){
    $this->setMiddlewares(new AuthenticationMiddleware(['setRate', 'getRate', 'uploadReview', 'deleteReview']));

    $this->reviews = new Reviews();
  }

  /**
   * This method shows the review course page.
   * @return View
   */
  public function review(){
    return $this->display('review','review', []);
  }

  /**
   * This method handles getting review by course ID.
   * @param Request sanitized request from the user.
   * @return JSON encoded string 200(OK).
   */
  public function getReview(Request $request){
    $body = $request->getBody();
    $courseID = $body['courseID'];
    $userID = Session::get(SESSION_USERID);

    $result = $this->reviews->getReview($userID, $courseID);
    $resp = ['success'=>true,'data'=>['result' => $result]];
    return $this->jsonResponse($resp, 200);
  }

  /**
   * This method handles deleting reviews by course ID and user ID (many to many table).
   * @param Request sanitized request from the user.
   */
  public function deleteReview(Request $request){
    $body = $request->getBody();

    $courseID = $body['courseID'];
    $userID = $body['userID'];

    if($userID == Session::get(SESSION_USERID)){
      $this->reviews->deleteReview($userID, $courseID);
      Application::$app->redirect("/UniShare/courses?ID=$courseID");
    }else{
      Application::$app->redirect("/UniShare/courses?ID=$courseID&error=failedremove");
    }
  }

  /**
   * This method handles uploading reviews.
   * @param Request sanitized request from the user.
   * @return JSON encoded string 500(generic error response)
   */
  public function uploadReview(Request $request){
    $body = $request->getBody();

    $params = [
      "courseID" => $body["courseID"],
      "fulfilling" => $body["fulfilling"],
      "environment" => $body["environment"],
      "difficulty" => $body["difficulty"],
      "grading" => $body["grading"],
      "litterature" => $body["litterature"],
      "overall" => $body["overall"],
      "text" => $body["text"],
    ];

    $errors = $this->reviews->validate($params);

    $courseID = $params['courseID'];
    if(count($errors) > 0){
      $errorList = http_build_query(array('error' => $errors));
      Application::$app->redirect("/UniShare/review?ID=$courseID&$errorList");
      exit();
    }

    $success = $this->reviews->insertReview($params);

    if($success){
      Application::$app->redirect("/UniShare/courses?ID=$courseID");
      exit();
    }else{
      //TODO check if this is nescessary.
      $resp = ['success'=>false,'data'=>['Status'=>'Failed upload review']];
      return $this->jsonResponse($resp, 500);
    }
  }
}