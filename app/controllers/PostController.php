<?php

namespace App\controllers;

use App\core\Exceptions\NotFoundException;
use App\Core\Request;
use App\Middleware\AuthenticationMiddleware;
use App\Models\Forums;
use App\Models\Posts;
use App\Core\Session;
use App\Core\Application;

/**
 * Post controller for handling posts.
 * @author Viggo Lagestedt Ekholm
 */
class PostController extends Controller
{
    private Posts $posts;
    private Forums $forums;

    function __construct()
    {
        $this->setMiddlewares(new AuthenticationMiddleware(['view', 'update', 'post', 'delete', 'addForum']));

        $this->posts = new Posts();
        $this->forums = new Forums();
    }

    /**
     * This method shows the post add page.
     * @param Request $request
     * @return string
     * @throws NotFoundException
     */
    public function view(Request $request): string
    {
        if (isset($_GET["ID"])) {
            $ID = $_GET["ID"];
            if (!empty($ID)) {
                $body = $request->getBody();

                if(!$this->forums->getForum($ID)){
                    return throw new NotFoundException();
                }

                $params = [
                    'forumID' => $body['ID'],
                ];
                return $this->display('post/add', 'post', $params);
            }
        }
        return throw new NotFoundException();
    }

    /**
     * This method handles adding new posts.
     * @param Request $request
     */
    public function addPost(Request $request)
    {
        $body = $request->getBody();

        $forumID = $body['forumID'];
        $text = $body['text'];
        $userID = Session::get(SESSION_USERID);

        $errors = $this->posts->validate($body);

        if (count($errors) > 0) {
            $errorList = http_build_query(array('error' => $errors));
            Application::$app->redirect("/9.0/post?ID=$forumID&$errorList");
            exit();
        }

        $inserted = $this->posts->addPost($userID, $forumID, $text);

        if ($inserted) {
            Application::$app->redirect("/9.0/forum?ID=$forumID");
        } else {
            Application::$app->redirect("/9.0/post?ID=$forumID&error=failed");
        }
    }
}
