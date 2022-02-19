<?php

namespace Source\Api;

use DateTime;
use DateTimeZone;
use MelquesPaiva\RestResponse\Response;
use Source\Core\Controller;
use Source\Models\Token;
use Source\Models\User;

abstract class AbstractApi extends Controller
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

        // Realizando recuperação de token na header Authorization
        $authorization = $this->headers["Authorization"] ?? null;
        if (!$authorization) {
            $this->response->actionForbidden("To make requests to the API, it is necessary to have an authentication token");
            exit;
        }

        // Verificando se o token está no padrão Bearer {token}
        if (!preg_match('/Bearer\s(\S+)/', $authorization, $matches)) {
            $this->response->unauthorized("The authorization token is invalid", "token");
            exit;
        }

        // Decodificiando o token enviando e verificando sua valida
        $userDataToken = Token::decodeToken($matches[1]);
        if (!$userDataToken) {
            $this->response->unauthorized("The authorization is not valid anymore", "token");
            exit;
        }

        // Verificando se o token ainda não expirou
        $currentDate = new DateTime('now', (new DateTimeZone(CONF_DATE_TIMEZONE)));
        $expTokenDate = new DateTime(date("Y-m-d H:i:s", $userDataToken->exp));
        if ($currentDate > $expTokenDate) {
            $this->response->unauthorized("The authorization token has expired", "token_date");
            exit;
        }

        // Recuperando um objeto usuário a partir dos dados enviados pelo token
        $this->user = (new User())->findOneById($userDataToken->data->id);
        if (!$this->user) {
            $this->response->unauthorized("The authorization is not valid. A user was not found", "token");
            exit;
        }
    }
}
