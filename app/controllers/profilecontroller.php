<?php
namespace App\Controllers;
use App\Core\Application;
use App\Models\MVCModels\Profiles;
use App\Models\MVCModels\Users;
use App\Models\MVCModels\Projects;
use App\Core\Request;
use App\Core\Session;
use App\Includes\Validate;
use App\Includes\Constants;
use App\Models\Project;
use App\Middleware\AuthenticationMiddleware;

class ProfileController extends Controller
{
  public function __construct(){
    $this->setMiddlewares(new AuthenticationMiddleware(['uploadImage', 'uploadProject', 'deleteProject', 'pubishCourse']));
  }

  public function view()
  {
    $profile = new Profiles();
    $users = new Users();
    $projects = new Projects();

    $ID = $_GET["ID"];

    if(!empty($ID)){
      $user = $users->getUser($ID);
      $first_name = $user["userFirstName"];
      $last_name = $user["userLastName"];
      $image = base64_encode($user["userImage"]);
      $updatedVisitCount = $profile->addVisitor($ID, $user);
      $projects = $projects->getProjects($ID);
      $user_image = 'data:image/jpeg;base64,' . $image;

      $date = $user["lastOnline"];

      if(Session::isLoggedIn()){
        $sessionID = Session::get('userID');
        if($ID == $sessionID){
          $date = $profile->addVisitDate($sessionID);
        }
      }

      $params = [
        'image' => $image,
        'updatedVisitCount' => $updatedVisitCount,
        'projects' => $projects,
        'user_image' => $user_image,
        'currentPageID' => $ID,
        'profile' => $profile,
        'visitDate' => $date,
        'first_name' => $first_name,
        'last_name' => $last_name
      ];
      return $this->display('profile', $params);
    }
    header("location: ./");
  }

  public function uploadImage(Request $request){
    $validImage = Validate::validateImage('file', Constants::MAX_UPLOAD_SIZE);

    if($validImage != 0){
      $ID = Session::get('userID');
      $profile = new Profiles();
      $profile->uploadImage($validImage, $ID);
    }else{
      $ID = Session::get('userID');
      header("location: ../../profile?ID=$ID&error=invalidupload");
    }
  }

  public function uploadProject(Request $request)
  {
    $validImage = Validate::validateImage('project-file', Constants::MAX_UPLOAD_SIZE);
    if($validImage != 0){
        $ID = Session::get('userID');

        $projects = new Projects();
        $project = new Project();

        $project->populateAttributes($request->getBody());
        $project->image = $validImage;
        $projects->uploadProject($project, $ID);
      }
    else{
      $ID = Session::get('userID');
      header("location: ../../profile?ID=$ID&error=invalidupload");
    }
  }

  public function deleteProject(Request $request){
    $projects = new Projects();
    $MAXID = $projects->GetMaxID();
    foreach($request->getBody() as $key => $value){
       //Delete the feed if it matches the ID of the clicked feed.
       $projects->deleteProject($key, Session::get('userID'));
     }
     header("location: ../../profile?ID=$ID");
  }


  public function pubishCourse(Request $request){
    Application::$app->request->getBody();

    return 'Handling submitted course data.';
  }

  public function pubishDegree(Request $request){
    Application::$app->request->getBody();

    return 'Handling submitted degree data.';
  }
}
