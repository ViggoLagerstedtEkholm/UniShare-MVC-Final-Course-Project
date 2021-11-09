<?php

namespace App\controllers;

use App\Models\Users;
use App\Models\Courses;
use App\Models\Forums;
use App\Core\Session;

/**
 * Home controller for handling the home page.
 * @author Viggo Lagestedt Ekholm
 */
class HomeController extends Controller
{
    private Users $users;
    private Courses $courses;
    private Forums $forums;

    public function __construct()
    {
        $this->users = new Users();
        $this->courses = new Courses();
        $this->forums = new Forums();
    }

    /**
     * This method shows the home page.
     * @return string
     */
    public function view(): string
    {
        $topRankedCourses = $this->courses->getTOP10Courses();
        $topViewedForums = $this->forums->getTOP10Forums();

        $currentUser = NULL;
        if (Session::exists(SESSION_USERID)) {
            $ID = Session::get(SESSION_USERID);
            $currentUser = $this->users->getUser($ID);
        }

        $params = [
            "currentUser" => $currentUser,
            "courses" => $topRankedCourses,
            "forums" => $topViewedForums
        ];

        return $this->display('startpage', 'startpage', $params);
    }
}
