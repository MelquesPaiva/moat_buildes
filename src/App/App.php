<?php

namespace Source\App;

use Source\Core\Controller;

class App extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $userSession = $this->session->user;

        if (empty($userSession)) {
            $this->message->error("You neet to login to access this application")->flash();
            redirect("/login");
        }
    }

    public function appPage(): void
    {
        echo $this->view->render("app", ["head" => ""]);
    }

    public function session(): void
    {
        echo json_encode(["user" => $this->session->user]);
    }
}