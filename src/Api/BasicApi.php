<?php

namespace Source\Api;

use MelquesPaiva\RestResponse\Response;
use Source\Core\Controller;

abstract class BasicApi extends Controller
{
    /** @var Response $response */
    protected Response $response;

    /** @var array $headers */
    protected array $headers;

    /**
     * Api Constructor
     */
    public function __construct()
    {
        parent::__construct();

        header("Content-type: application/json; charset=UTF-8");

        $this->response = new Response();
        $this->headers = getallheaders();
    }
}
