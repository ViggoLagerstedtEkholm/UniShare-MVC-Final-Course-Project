<?php

namespace App\controllers;

use App\Core\Exceptions\GDResizeException;
use App\core\Exceptions\NotFoundException;
use App\includes\ImageValidator;
use App\Models\Projects;
use App\Core\Request;
use App\Core\Session;
use App\Core\Application;
use App\Core\ImageHandler;
use App\Middleware\AuthenticationMiddleware;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Project controller for handling projects.
 * @author Viggo Lagestedt Ekholm
 */
class ProjectController extends Controller
{
    private Projects $projects;
    private ImageHandler $imageHandler;

    function __construct()
    {
        $this->setMiddlewares(new AuthenticationMiddleware(['view', 'uploadProject', 'deleteProject', 'getProject', 'updateProject']));

        $this->projects = new Projects();
        $this->imageHandler = new ImageHandler();
    }

    /**
     * This method shows the project add page.
     * @return string
     */
    public function add(): string
    {
        return $this->display('projects/add', 'projects', []);
    }

    /**
     * This method shows the project update page.
     * @return string
     * @throws NotFoundException
     */
    public function update(): string
    {
        if (isset($_GET["ID"])) {
            $ID = $_GET["ID"];

            $params = [
                "projectID" => $ID
            ];

            return $this->display('projects/update', 'projects', $params);
        } else {
            Application::$app->redirect("./");
        }

        return throw new NotFoundException();
    }

    /**
     * This method handles adding new projects.
     * @param Request $request
     * @throws GDResizeException
     */
    public function uploadProject(Request $request)
    {
        $fileUploadName = 'project-file';
        $userID = Session::get(SESSION_USERID);

        //Check all fields + image validity
        $result = $this->validateUpload($request, $fileUploadName);
        $errors = $result['errors'];
        $params = $result['params'];

        if (count($errors) > 0) {
            $errorList = http_build_query(array('error' => $errors));
            Application::$app->redirect("/9.0/project/add?&$errorList");
            exit();
        }

        if ($params["customCheck"] == "On") {
            $image = $this->imageHandler->createImageFromText($params["text"]);
        } else {
            $originalImage = $_FILES[$fileUploadName];
            $image = $this->imageHandler->handleUploadResizing($originalImage);
        }

        $this->projects->uploadProject($params, $userID, $image);
        Application::$app->redirect("/9.0/profile?ID=$userID");
    }

    /**
     * Update project.
     * @param Request $request
     * @return void
     * @throws GDResizeException
     */
    public function updateProject(Request $request)
    {
        $fileUploadName = 'project-file';
        $userID = Session::get(SESSION_USERID);

        $body = $request->getBody();
        $projectID = $body["projectID"];

        $result = $this->validateUpload($request, $fileUploadName);
        $errors = $result['errors'];
        $params = $result['params'];

        if (!ImageValidator::hasValidUpload($fileUploadName) && $params["customCheck"] != "On")
        {
            if (!ImageValidator::hasValidImageExtension($fileUploadName))
            {
                $errors[] = INVALID_IMAGE;
            }
        }

        if (count($errors) > 0) {
            $errorList = http_build_query(array('error' => $errors));
            Application::$app->redirect("/9.0/project/update?ID= $projectID&$errorList");
            exit();
        }

        $canUpdate = $this->projects->checkIfUserOwner($userID, $projectID);

        if ($canUpdate && !empty($projectID)) {
            if ($params["customCheck"] == "On") {
                $image = $this->imageHandler->createImageFromText($params["text"]);
            } else {
                $originalImage = $_FILES[$fileUploadName];
                $image = $this->imageHandler->handleUploadResizing($originalImage);
            }
            $this->projects->updateProject($projectID, $params, $image);
            $TEST = $params["customCheck"];
            Application::$app->redirect("../profile?ID=$userID&test=$TEST");
        } else {
            Application::$app->redirect("../");
        }
    }

    /**
     * Validate the uploaded project.
     * @param Request $request
     * @param string $fileUploadName
     * @return array
     */
    #[ArrayShape(['errors' => "array", 'params' => "array"])]
    private function validateUpload(Request $request, string $fileUploadName): array
    {
        $body = $request->getBody();

        if ($body["customCheck"] == "On") {
            $params = [
                "link" => $body["link"],
                "name" => $body["name"],
                "description" => $body["description"],
                "text" => $body["text"],
                "customCheck" => $body["customCheck"]
            ];
        } else {
            $params = [
                "link" => $body["link"],
                "name" => $body["name"],
                "file" => $fileUploadName,
                "description" => $body["description"],
                "customCheck" => $body["customCheck"]
            ];

        }

        $errors = $this->projects->validate($params);

        return [
            'errors' => $errors,
            'params' => $params
        ];
    }

    /**
     * This method handles deleting projects.
     * @param Request $request
     * @return false|string
     */
    public function deleteProject(Request $request): bool|string
    {
        $courseRequest = $request->getBody();

        $projectID = $courseRequest["projectID"];
        $canRemove = $this->projects->checkIfUserOwner(Session::get(SESSION_USERID), $projectID);

        if ($canRemove) {
            $this->projects->deleteProject($projectID);
            $resp = ['success' => true, 'data' => ['Status' => true, 'ID' => $projectID]];
            return $this->jsonResponse($resp, 200);
        } else {
            $resp = ['success' => false, 'data' => ['Status' => false, 'ID' => $projectID]];
            return $this->jsonResponse($resp, 500);
        }
    }

    /**
     * This method gets project that we want to edit.
     * @param Request $request
     * @return false|string
     */
    public function getProjectForEdit(Request $request): bool|string
    {
        $body = $request->getBody();
        $projectID = $body["projectID"];

        if (empty($projectID)) {
            $resp = ['success' => false, 'status' => 'No matching ID!'];
            return $this->jsonResponse($resp, 404);
        }

        $project = $this->projects->getProject($projectID);

        //Check if the currently logged in user is the one that owns the project.
        if ($project["userID"] == Session::get(SESSION_USERID)) {
            $name = $project["name"];
            $link = $project["link"];
            $description = $project["description"];
            $resp = ['success' => true, 'data' => ['Name' => $name, 'Link' => $link, 'Description' => $description]];
            return $this->jsonResponse($resp, 200);
        } else {
            $resp = ['success' => false, 'data' => ['Project' => $project]];
            return $this->jsonResponse($resp, 403);
        }
    }
}
