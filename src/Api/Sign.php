<?php

namespace Source\Api;

use Source\Models\Sign as SignModel;
use Source\Models\User;

/**
 * @package Source\Api
 */
class Sign extends BasicApi
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login(): void
    {
        $data = $this->jsonData();

        $userName = $data->userName ?? null;
        $password = $data->password ?? null;

        if (empty($userName) || empty($password)) {
            $this->response->badRequest("You need to inform the user and password to Login", "user");
            return;
        }
        
        $login = new SignModel($userName, $password);
        $userLogged = $login->login();

        if (empty($userLogged)) {
            $this->response->unauthorized(
                "Sorry, we couldn't find an account with this username. Please check you're using the right username and try again.",
                "user"
            );
            return;
        }

        $this->session->set("user", $userLogged);
        $this->response->successful("Login was realized successfully", ["token" => $userLogged->token]);
    }

    public function register(): void
    {
        $data = $this->jsonData();

        $user = new User();

        $user->user_name = $data->userName ?? null;
        $user->full_name = $data->fullName ?? null;
        $user->password = $data->password ?? null;
        $confirmPass = $data->passwordConfirmation ?? null;
        $user->role = 1;

        if ($user->password != $confirmPass) {
            $this->response->badRequest("Passwords don't match", "user_password");
            return;
        }

        $userSaved = $user->save();

        if ($user->fail()) {
            $this->response->internalError($user->message()->getMessage());
            return;
        }

        if (!$userSaved) {
            $this->response->badRequest($user->message()->getMessage(), "user");
            return;
        }

        $this->response->successful("The user was registered successfully. Go to the login page and access the application", [
            "id" => $user->id,
            "user_name" => $user->user_name,
            "full_name" => $user->full_name,
        ]);
    }
}
