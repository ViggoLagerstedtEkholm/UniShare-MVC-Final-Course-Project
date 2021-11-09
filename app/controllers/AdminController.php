<?php

namespace App\controllers;

use App\Core\Request;
use App\Models\Courses;
use App\Models\Requests;
use App\Middleware\AuthenticationMiddleware;
use Throwable;

/**
 * Admin controller for administering website content.
 * @author Viggo Lagestedt Ekholm
 */
class AdminController extends Controller
{
    private Courses $courses;
    private Requests $requests;

    public function __construct()
    {
        //Add restriction to all methods and set the last parameter as true (require admin for whole controller).
        $this->setMiddlewares(new AuthenticationMiddleware(['view', 'updateUser', 'removeUser', 'addUser', 'addCourse' . 'removeCourse', 'updateCourse'], true));

        $this->courses = new Courses();
        $this->requests = new Requests();
    }

    /**
     * This method gets the requested courses and passes them to the view.
     */
    public function view(): string
    {
        $requests = $this->requests->getRequestedCourses();

        $params = [
            "requests" => $requests
        ];

        return $this->display('admin', 'admin', $params);
    }

    /**
     * This method handles approving requested courses from users.
     * @param Request $request
     * @return false|string
     * @throws Throwable
     */
    public function approveRequest(Request $request): bool|string
    {
        $body = $request->getBody();
        $requestID = $body["requestID"];

        $success = $this->requests->approveRequest($requestID);

        if ($success) {
            $resp = ['success' => true, 'data' => ['Status' => true, 'ID' => $requestID]];
            return $this->jsonResponse($resp, 200);
        } else {
            $resp = ['success' => false, 'data' => ['Status' => false]];
            return $this->jsonResponse($resp, 500);
        }
    }

    /**
     * This method handles denying requested courses from users.
     * @param Request $request
     * @return false|string
     */
    public function denyRequest(Request $request): bool|string
    {
        $body = $request->getBody();
        $requestID = $body["requestID"];

        $success = $this->requests->denyRequest($requestID);

        if ($success) {
            $resp = ['success' => true, 'data' => ['Status' => true, 'ID' => $requestID]];
            return $this->jsonResponse($resp, 200);
        } else {
            $resp = ['success' => false, 'data' => ['Status' => false]];
            return $this->jsonResponse($resp, 500);
        }
    }
}
