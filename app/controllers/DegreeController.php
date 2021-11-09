<?php

namespace App\controllers;

use App\Core\Application;
use App\core\Exceptions\NotFoundException;
use App\Core\Session;
use App\Core\Request;
use App\Middleware\AuthenticationMiddleware;
use App\models\Degrees;
use JetBrains\PhpStorm\NoReturn;

/**
 * Degree controller for handling degrees.
 * @author Viggo Lagestedt Ekholm
 */
class DegreeController extends Controller
{
    private Degrees $degrees;

    public function __construct()
    {
        $this->setMiddlewares(new AuthenticationMiddleware(
            ['view', 'uploadDegree', 'deleteDegree', 'updateDegree', 'getdegrees']));

        $this->degrees = new Degrees();
    }

    /**
     * This method shows the add degree page.
     * @return string
     */
    public function add(): string
    {
        return $this->display('degrees/add', 'degrees', []);
    }

    /**
     * This method shows the update degree page.
     * @return string
     * @throws NotFoundException
     */
    public function update(): string
    {
        if (isset($_GET["ID"])) {
            $ID = $_GET["ID"];
            if (!empty($ID)) {

                if (!$this->degrees->getDegree($ID)) {
                    return throw new NotFoundException();
                }

                $params = [
                    'degreeID' => $ID
                ];

                return $this->display('degrees/update', 'degrees', $params);
            }
        }
        return throw new NotFoundException();
    }

    /**
     * This method gets the degrees from the logged in user.
     * @return false|string
     */
    public function getDegrees(): false|string
    {
        $degrees = $this->degrees->getDegrees(Session::get(SESSION_USERID));
        $resp = ['success' => true, 'data' => ['degrees' => $degrees]];
        return $this->jsonResponse($resp, 200);
    }

    /**
     * This method gets a specific degree from ID from the logged in user.
     * @param Request $request
     * @return false|string
     */
    public function getDegree(Request $request): false|string
    {
        $body = $request->getBody();
        $degreeID = $body["degreeID"];

        if (!empty($degreeID)) {
            $degree = $this->degrees->getDegree($degreeID);
        } else {
            $resp = ['success' => false, 'status' => 'No matching ID!'];
            return $this->jsonResponse($resp, 404);
        }

        if (!is_null($degree)) {
            $resp = ['success' => true, 'data' => ['degree' => $degree[0]]];
            return $this->jsonResponse($resp, 200);
        } else {
            $resp = ['success' => false];
            return $this->jsonResponse($resp, 500);
        }
    }

    /**
     * This method handles uploading a degree to a user.
     * @param Request $request
     */
    public function uploadDegree(Request $request)
    {
        $body = $request->getBody();
        $userID = Session::get(SESSION_USERID);

        $errors = $this->validateUpload($request);

        if (count($errors) > 0) {
            $errorList = http_build_query(array('error' => $errors));
            Application::$app->redirect("/9.0/degree/new?$errorList");
            exit();
        }

        $this->degrees->uploadDegree($body, Session::get(SESSION_USERID));
        Application::$app->redirect("/9.0/profile?ID=$userID");
    }


    /**
     * This method updates a specific degree from ID from the logged in user.
     * @param Request $request
     */
    #[NoReturn] public function updateDegree(Request $request)
    {
        $body = $request->getBody();
        $degreeID = $body["degreeID"];
        $userID = Session::get(SESSION_USERID);

        $errors = $this->validateUpload($request);

        if (count($errors) > 0) {
            $errorList = http_build_query(array('error' => $errors));
            Application::$app->redirect("/9.0/degree/update?ID=$degreeID&$errorList");
            exit();
        }

        $canUpdate = $this->degrees->checkIfUserOwner($userID, $degreeID);

        if ($canUpdate) {
            $this->degrees->updateDegree($body, $userID, $degreeID);
            Application::$app->redirect("/9.0/profile?ID=$userID");
        } else {
            Application::$app->redirect("/9.0/");
        }
    }

    /**
     * Validate the uploaded fields.
     * @param Request $request
     * @return array
     */
    private function validateUpload(Request $request): array
    {
        $body = $request->getBody();

        $params = [
            "name" => $body["name"],
            "field_of_study" => $body["field_of_study"],
            "start_date" => $body["start_date"],
            "end_date" => $body["end_date"],
            "country" => $body["country"],
            "city" => $body["city"],
            "university" => $body["university"],
        ];

        return $this->degrees->validate($params);
    }

    /**
     * This method deletes a specific degree from ID from the logged in user.
     * @param Request $request
     * @return false|string
     */
    public function deleteDegree(Request $request): bool|string
    {
        $body = $request->getBody();
        $degreeID = $body['degreeID'];
        $userID = Session::get(SESSION_USERID);

        $canDelete = $this->degrees->checkIfUserOwner($userID, $degreeID);

        if ($canDelete) {
            $this->degrees->deleteDegree($degreeID);
            $resp = ['success' => true, 'data' => ['degreeID' => $degreeID]];
            return $this->jsonResponse($resp, 200);
        } else {
            $resp = ['success' => false];
            return $this->jsonResponse($resp, 401);
        }
    }
}
