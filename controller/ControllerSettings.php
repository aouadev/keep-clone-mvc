<?php

require_once "framework/Controller.php";
require_once "framework/View.php";
require_once "model/User.php";

class ControllerSettings extends Controller
{

    public function settings(): void
    {
        $user = $this->get_user_or_redirect();
        $sharers = $user->shared_by();

        (new View("settings"))->show(["currentPage" => "settings", "user" => $user, "sharers" => $sharers]);
    }

    public function edit_profile(): void {
        $user = $this->get_user_or_redirect();
        $mail = $user->mail;
        $errors = [];
        $name = $user->full_name;
      
        if (isset($_POST['mail']) && isset($_POST['name'])) { //&& isset($_POST['password'])) {
            $mail = $_POST['mail'];
                if($mail != $user->mail) {
                    $errors = $user->validate_unicity($mail);
                    $errors = array_merge($errors, $user->validate($mail));
                }
                $name = $_POST['name'];
                $errors = array_merge($errors, $user->validate_name($name));
                if (empty($errors)) {
                    $user->full_name = $name;
                    $user->mail = $mail;
                    $user->edit_profile($mail, $name);
                    $this->redirect("settings", "settings");
                }
            }
            (new View("edit_profile"))->show(["user"=>$user, "mail" => $mail, "name" => $name, "errors" => $errors]);
        }


    public function change_password(): void
    {
        $user = $this->get_user_or_redirect();

        $successMessage = null;
        $errors[] = [];
        $sharers = $user->shared_by();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['currentPassword'];
            $newPassword = $_POST['newPassword'];
            $confirmNewPassword = $_POST['confirmNewPassword'];


            $errors = User::validate_login($user->mail, $currentPassword);

            if (empty($errors)) {

                $passwordErrors = User::validate_passwords($newPassword, $confirmNewPassword, $user);

                if (empty($passwordErrors)) {

                    try {
                        $user->setPassword($newPassword);
                        $user->updatePassword($newPassword);
                        $successMessage = "Password changed successfully!";
                        $this->redirect("settings", "settings");
                    } catch (Exception $e) {
                        $errors[] = "Erreur lors de la mise à jour du mot de passe : " . $e->getMessage();
                    }
                } else {
                    $errors = array_merge($errors, $passwordErrors);
                }
            }
            (new View("change_password"))->show(["user" => $user, "successMessage" => $successMessage, "errors" => $errors, "sharers" => $sharers]);
        } else {
            (new View("change_password"))->show(["user" => $user, "sharers" => $sharers]);
        }
    }


    public function index(): void
    {
    }
}
