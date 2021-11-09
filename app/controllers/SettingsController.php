<?php

namespace App\controllers;

use App\includes\UserValidation;
use App\Middleware\AuthenticationMiddleware;
use App\Models\Users;
use App\Core\Session;
use App\Core\Request;
use App\Core\Application;

/**
 * Settings controller for handling user settings.
 * @author Viggo Lagestedt Ekholm
 */
class SettingsController extends Controller
{
    private Users $users;

    function __construct()
    {
        $this->setMiddlewares(new AuthenticationMiddleware(['view', 'deleteAccount', 'getSettings', 'update']));

        $this->users = new Users();
    }

    /**
     * This method shows the user settings page.
     * @return string
     */
    public function view(): string
    {
        return $this->display('settings', 'settings', []);
    }

    /**
     * This method handles updating the settings and validating inputs.
     * @param Request $request
     */
    public function update(Request $request)
    {
        $updatedInfo = $request->getBody();

        $updated_first_name = $updatedInfo["first_name"];
        $updated_last_name = $updatedInfo["last_name"];
        $updated_email = $updatedInfo["email"];
        $updated_display_name = $updatedInfo["display_name"];
        $updated_current_password = $updatedInfo["current_password"];
        $updated_new_password = $updatedInfo["new_password"];
        $updated_activeDegreeID = $updatedInfo["activeDegreeID"];
        $updated_description = $updatedInfo["description"];

        $user = $this->users->getUser(Session::get(SESSION_USERID));
        $first_name = $user["userFirstName"];
        $last_name = $user["userLastName"];
        $email = $user["userEmail"];
        $display_name = $user["userDisplayName"];
        $passwordHash = $user["usersPassword"];
        $activeDegreeID = $user["activeDegreeID"];
        $description = $user["description"];

        $errors = array();

        if (!empty($updated_current_password) && !empty($updated_new_password)) {
            $comparePassword = password_verify($updated_current_password, $passwordHash);

            if ($comparePassword === false) {
                $errors[] = INVALID_PASSWORD_MATCH;
            } else {
                $hashedNewPassword = password_hash($updated_new_password, PASSWORD_DEFAULT);
                $this->users->updateUser("usersPassword", $hashedNewPassword, Session::get(SESSION_USERID));
            }
        }

        //Check existing users information.
        if ($updated_email !== $email && !is_null($this->users->userExists( "userEmail", $updated_email))) {
            $errors[] = EMAIL_TAKEN;
        }
        if ($updated_display_name !== $display_name && !is_null($this->users->userExists("userDisplayName", $updated_display_name))) {
            $errors[] = USERNAME_TAKEN;
        }

        if (!UserValidation::validEmail($updated_email)) {
            $errors[] = INVALID_EMAIL;
        }
        if (!UserValidation::validFirstname($updated_first_name)) {
            $errors[] = INVALID_FIRST_NAME;
        }
        if (!UserValidation::validLastname($updated_last_name)) {
            $errors[] = INVALID_LAST_NAME;
        }
        if (!UserValidation::validUsername($updated_display_name)) {
            $errors[] = INVALID_USERNAME;
        }
        if(!UserValidation::validDescription($description)){
            $errors[] = INVALID_DESCRIPTION;
        }
        if(!UserValidation::validPassword($updated_new_password) && !empty($updated_new_password)){
            $errors[] = INVALID_PASSWORD;
        }

        if (count($errors) > 0) {
            $errorList = http_build_query(array('error' => $errors));
            Application::$app->redirect("/9.0/settings?$errorList");
            exit();
        }

        if ($updated_description != $description && !empty($updated_description)) {
            $this->users->updateUser("description", $updated_description, Session::get(SESSION_USERID));
        }
        if ($updated_activeDegreeID != $activeDegreeID && !empty($updated_activeDegreeID)) {
            $this->users->updateUser("activeDegreeID", $updated_activeDegreeID, Session::get(SESSION_USERID));
        }
        if ($updated_first_name != $first_name && !empty($updated_first_name)) {
            $this->users->updateUser("userFirstName", $updated_first_name, Session::get(SESSION_USERID));
        }
        if ($updated_last_name != $last_name && !empty($updated_last_name)) {
            $this->users->updateUser("userLastName", $updated_last_name, Session::get(SESSION_USERID));
        }
        if ($updated_email != $email && !empty($updated_email)) {
            $this->users->updateUser("userEmail", $updated_email, Session::get(SESSION_USERID));
        }
        if ($updated_display_name != $display_name && !empty($updated_display_name)) {
            $this->users->updateUser("userDisplayName", $updated_display_name, Session::get(SESSION_USERID));
        }

        $ID = Session::get(SESSION_USERID);
        Application::$app->redirect("../profile?ID=$ID");
    }

    /**
     * Get the current settings and return the fields.
     * @return false|string
     */
    public function fetch(): bool|string
    {
        $user = $this->users->getUser(Session::get(SESSION_USERID));
        $first_name = $user["userFirstName"];
        $last_name = $user["userLastName"];
        $email = $user["userEmail"];
        $display_name = $user["userDisplayName"];
        $description = $user["description"];

        $resp = ['success' => true, 'data' => ['email' => $email, 'first_name' => $first_name, 'last_name' => $last_name, 'display_name' => $display_name, 'description' => $description]];

        return $this->jsonResponse($resp, 200);
    }

    /**
     * Delete the user account.
     */
    public function deleteAccount()
    {
        $userID = Session::get(SESSION_USERID);

        $this->users->terminateAccount($userID);
        $this->users->logout();
        Application::$app->redirect("/9.0/");
    }
}
