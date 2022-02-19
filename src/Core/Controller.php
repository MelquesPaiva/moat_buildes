<?php

namespace Source\Core;

use Source\Support\Message;
use stdClass;

/**
 * Abstract Class Controller
 * @package Source\Core
 */
abstract class Controller
{
    /** @var View $view */
    protected View $view;

    /** @var Session $session */
    protected Session $session;

    /** @var Message $message */
    protected Message $message;

    /**
     * Controller Constructor
     *
     * @param string|null $pathToViews
     */
    public function __construct(string $pathToViews = CONF_VIEW_PATH)
    {
        $this->view = new View($pathToViews, CONF_VIEW_EXT);
        $this->session = new Session();
        $this->message = new Message();
    }

    /**
     * Método HTTP
     *
     * @return string
     */
    public function method(): string
    {
        return filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    }

    /**
     * Get payload data from request
     *
     * @return stdClass|null
     */
    protected function jsonData(): ?stdClass
    {
        $jsonData = json_decode(file_get_contents("php://input"));
        if (is_array($jsonData)) {
            $jsonData = (object) $jsonData;
        }

        return $jsonData;
    }
}
