<?php

namespace App\controllers;

use App\Core\Request;
use App\Models\Requests;
use App\Core\Session;
use App\Middleware\AuthenticationMiddleware;
use App\Core\Application;

/**
 * Request controller for handling course requests.
 * @author Viggo Lagestedt Ekholm
 */
class RequestController extends Controller
{
    private Requests $requests;

    function __construct()
    {
        $this->setMiddlewares(new AuthenticationMiddleware(['view', 'uploadRequest', 'deletePending']));

        $this->requests = new Requests();
    }

    /**
     * This method shows the request course page.
     * @return string
     */
    public function view(): string
    {
        $requests = $this->requests->getRequestedCourses();

        $params = [
            "requests" => $requests
        ];

        return $this->display('request', 'request', $params);
    }

    /**
     * This method handles uploading new course requests.
     * @param Request $request
     */
    public function uploadRequest(Request $request)
    {
        $courseRequest = $request->getBody();

        $params = [
            "name" => $courseRequest["name"],
            "credits" => $courseRequest["credits"],
            "country" => $courseRequest["country"],
            "city" => $courseRequest["city"],
            "university" => $courseRequest["university"],
            "description" => $courseRequest["description"]
        ];

        $errors = $this->requests->validate($params);

        if (count($errors) > 0) {
            $query = http_build_query(array('error' => $errors));
            Application::$app->redirect("../request?$query");

        } else {
            $this->requests->insertRequestedCourse($params, Session::get(SESSION_USERID));
            Application::$app->redirect("../request?error=none");
        }
    }

    /**
     * This method handles deleting pending requests.
     * @param Request $request
     * @return false|string
     */
    function deletePending(Request $request): bool|string
    {
        $courseRequest = $request->getBody();

        $requestID = $courseRequest["requestID"];
        $canRemove = $this->requests->checkIfUserOwner(Session::get(SESSION_USERID), $requestID);

        if ($canRemove) {
            $this->requests->deleteRequest($requestID);
            $resp = ['success' => true, 'data' => ['Status' => true, 'ID' => $requestID]];
            return $this->jsonResponse($resp, 200);
        } else {
            $resp = ['success' => false, 'data' => ['Status' => false, 'ID' => $requestID]];
            return $this->jsonResponse($resp, 500);
        }
    }
}
