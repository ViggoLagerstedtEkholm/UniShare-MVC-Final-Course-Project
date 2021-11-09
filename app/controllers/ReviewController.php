<?php

namespace App\controllers;

use App\core\Exceptions\NotFoundException;
use App\Middleware\AuthenticationMiddleware;
use App\models\Courses;
use App\Models\Reviews;
use App\Core\Session;
use App\Core\Request;
use App\Core\Application;

/**
 * Review controller for handling reviews.
 * @author Viggo Lagestedt Ekholm
 */
class ReviewController extends Controller
{
    private Reviews $reviews;
    private Courses $courses;

    function __construct()
    {
        $this->setMiddlewares(new AuthenticationMiddleware(['setRate', 'getRate', 'uploadReview', 'deleteReview']));

        $this->reviews = new Reviews();
        $this->courses = new Courses();
    }

    /**
     * This method shows the review course page.
     * @return string
     * @throws NotFoundException
     */
    public function review(): string
    {
        if (isset($_GET["ID"])) {
            $ID = $_GET["ID"];
            if (!empty($ID)) {

                if (!$this->courses->getCourse($ID)) {
                    return throw new NotFoundException();
                }

                return $this->display('review', 'review', []);

            }
        }
        return throw new NotFoundException();
    }

    /**
     * This method handles getting review by course ID.
     * @param Request $request
     * @return false|string
     */
    public function getReview(Request $request): bool|string
    {
        $body = $request->getBody();
        $courseID = $body['courseID'];
        $userID = Session::get(SESSION_USERID);

        $result = $this->reviews->getReview($userID, $courseID);
        $resp = ['success' => true, 'data' => ['result' => $result]];
        return $this->jsonResponse($resp, 200);
    }

    /**
     * This method handles deleting reviews by course ID and user ID (many to many table).
     * @param Request $request
     */
    public function deleteReview(Request $request)
    {
        $body = $request->getBody();

        $courseID = $body['courseID'];
        $userID = $body['userID'];

        if ($userID == Session::get(SESSION_USERID)) {
            $this->reviews->deleteReview($userID, $courseID);
            Application::$app->redirect("/9.0/courses?ID=$courseID");
        } else {
            Application::$app->redirect("/9.0/courses?ID=$courseID&error=failedremove");
        }
    }

    /**
     * This method handles uploading reviews.
     * @param Request $request
     */
    public function uploadReview(Request $request)
    {
        $body = $request->getBody();

        $params = [
            "courseID" => $body["courseID"],
            "fulfilling" => $body["fulfilling"],
            "environment" => $body["environment"],
            "difficulty" => $body["difficulty"],
            "grading" => $body["grading"],
            "literature" => $body["litterature"],
            "overall" => $body["overall"],
            "text" => $body["text"],
        ];

        $errors = $this->reviews->validate($params);
        $courseID = $params['courseID'];

        if (count($errors) > 0) {
            $errorList = http_build_query(array('error' => $errors));
            Application::$app->redirect("/9.0/review?ID=$courseID&$errorList");
            exit();
        }

        $success = $this->reviews->insertReview($params);

        if($success){
            Application::$app->redirect("/9.0/courses?ID=$courseID");
        }else{
            Application::$app->redirect("/9.0/");
        }
    }
}
