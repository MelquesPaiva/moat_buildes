<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Models\User;

/**
 * @package Source\App
 */
class App extends Controller
{
    /**
     * App Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $userSession = $this->session->user;

        if (empty($userSession)) {
            $this->message->error("You need to login to access this application")->flash();
            redirect("/");
        }
    }

    /**
     * Render internal from the application
     *
     * @return void
     */
    public function appPage(): void
    {
        echo $this->view->render("app", ["head" => ""]);
    }

    /**
     * Return user data from session
     *
     * @return void
     */
    public function session(): void
    {
        $sessionId = $this->session->user->user->id;
        $user = (new User())->findOneById($sessionId);
        $userResult = (object) [
            "id" => $user->id,
            "role" => $user->role,
            "full_name" => $user->full_name,
        ];

        echo json_encode(["user" => $userResult]);
    }

    /**
     * Realize the logout from the application
     *
     * @return void
     */
    public function logout(): void
    {
        $this->session->unset('user');
        $this->session->destroy();

        redirect("/");
    }
}