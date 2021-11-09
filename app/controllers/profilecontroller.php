<?php

namespace App\controllers;

use App\Core\Application;
use App\Core\Exceptions\GDResizeException;
use App\core\Exceptions\NotFoundException;
use App\Core\Request;
use App\Core\Session;
use App\Core\ImageHandler;
use App\includes\ImageValidator;
use App\Models\Users;
use App\Models\Projects;
use App\Models\Degrees;
use App\Models\Comments;
use App\Middleware\AuthenticationMiddleware;

/**
 * Profile controller for handling profiles.
 * @author Viggo Lagestedt Ekholm
 */
class ProfileController extends Controller
{
    private ImageHandler $imageHandler;
    private Users $users;
    private Projects $projects;
    private Degrees $degrees;
    private Comments $comments;

    public function __construct()
    {
        $this->setMiddlewares(new AuthenticationMiddleware(['uploadImage', 'removeCourseFromDegree', 'addComment']));

        $this->imageHandler = new ImageHandler();
        $this->users = new Users();
        $this->projects = new Projects();
        $this->degrees = new Degrees();
        $this->comments = new Comments();
    }

    /**
     * This method shows the profile page.
     * @return string
     * @throws NotFoundException
     */
    public function view(): string
    {
        if (isset($_GET["ID"])) {
            $ID = $_GET["ID"];
            if (!empty($ID)) {

                if(!$this->users->getUser($ID)){
                    return throw new NotFoundException();
                }

                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                } else {
                    $page = 1;
                }

                $comment_count = $this->comments->getCommentCount($ID);

                $offsets = $this->calculateOffsets($comment_count, $page, 3);
                $start_page_first_result = $offsets['start_page_first_result'];
                $results_per_page = $offsets['results_per_page'];
                $number_of_pages = $offsets['number_of_pages'];

                $comments = $this->comments->getComments($start_page_first_result, $results_per_page, $ID);


                $user = $this->users->getUser($ID);
                $image = base64_encode($user["userImage"]);
                $degrees = $this->degrees->getDegrees($ID);
                $updatedVisitCount = $this->users->addVisitor($ID, $user);
                $projects = $this->projects->getProjects($ID);

                if (Session::isLoggedIn()) {
                    $sessionID = Session::get(SESSION_USERID);

                    if ($ID == $sessionID) {
                        $this->users->addVisitDate($sessionID);
                    }
                }

                $params = [
                    'image' => $image,
                    'comments' => $comments,
                    'degrees' => $degrees,
                    'updatedVisitCount' => $updatedVisitCount,
                    'projects' => $projects,
                    'page' => $page,
                    'results_per_page' => $results_per_page,
                    'number_of_pages' => $number_of_pages,
                    'start_page_first_result' => $start_page_first_result,
                    'currentPageID' => $ID,
                    'visitDate' => $user["lastOnline"],
                    'first_name' => $user["userFirstName"],
                    'last_name' => $user["userLastName"],
                    'display_name' => $user["userDisplayName"],
                    'privilege' => $user["privilege"],
                    'description' => $user["description"],
                    'joined' => $user["joined"]
                ];


                return $this->display('profile', 'profile', $params);
            }
        }
        return throw new NotFoundException();
    }

    /**
     * This method resizes and uploads the image.
     * @throws GDResizeException
     */
    public function uploadImage()
    {
        $fileUploadName = 'file';
        $sessionID = Session::get(SESSION_USERID);

        if (ImageValidator::hasValidUpload($fileUploadName))
        {
            if (ImageValidator::hasValidImageExtension($fileUploadName))
            {
                $originalImage = $_FILES[$fileUploadName];
                $image_resize = $this->imageHandler->handleUploadResizing($originalImage);
                $this->users->uploadImage($image_resize, $sessionID);
                Application::$app->redirect("../../profile?ID=$sessionID");
            }
        } else {

            Application::$app->redirect("../../profile?ID=$sessionID&error=" . INVALID_UPLOAD);
        }
    }

    /**
     * Delete comment.
     * @param Request $request
     * @return false|string
     */
    public function deleteComment(Request $request): bool|string
    {
        $body = $request->getBody();
        $commentID = $body['commentID'];

        $canRemove = $this->comments->checkIfUserAuthor(Session::get(SESSION_USERID), $commentID);

        if ($canRemove) {
            $this->comments->deleteComment($commentID);
            $resp = ['success' => true, 'data' => ['Status' => true, 'ID' => $commentID]];
            return $this->jsonResponse($resp, 200);
        } else {
            $resp = ['success' => true, 'data' => ['Status' => false, 'ID' => $commentID]];
            return $this->jsonResponse($resp, 500);
        }
    }

    /**
     * Add comment.
     * @param Request $request
     * @return false|string
     */
    public function addComment(Request $request): bool|string
    {
        $body = $request->getBody();

        $posterID = Session::get(SESSION_USERID);
        $text = $body['text'];
        $profileID = $body['pageID'];

        $succeeded = $this->comments->addComment($posterID, $profileID, $text);
        if ($succeeded) {
            $resp = ['success' => true, 'data' => ['Status' => 'Added comment']];
            return $this->jsonResponse($resp, 200);
        } else {
            $resp = ['success' => false, 'data' => ['Status' => 'Error']];
            return $this->jsonResponse($resp, 500);
        }
    }

    /**
     * Remove course from degree.
     * @param Request $request
     * @return false|string
     */
    public function removeCourseFromDegree(Request $request): bool|string
    {
        $courseRequest = $request->getBody();

        $courseID = $courseRequest["courseID"];
        $degreeID = $courseRequest["degreeID"];

        $succeeded = $this->degrees->checkIfUserOwner(Session::get(SESSION_USERID), $degreeID);

        if ($succeeded) {
            $this->degrees->deleteCourseFromDegree($degreeID, $courseID);
            $resp = ['success' => true, 'data' => ['Status' => true, 'ID' => $courseID, 'degreeID' => $degreeID]];
            return $this->jsonResponse($resp, 200);
        } else {
            $resp = ['success' => false, 'data' => ['Status' => 'Error']];
            return $this->jsonResponse($resp, 403);
        }
    }
}
