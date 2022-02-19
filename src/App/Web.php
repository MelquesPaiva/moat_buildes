<?php

namespace Source\App;

use Source\Core\Controller;

/**
 * Class Web
 * @package Source\App
 */
class Web extends Controller
{
    /**
     * Web Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function login(): void
    {
        echo $this->view->render('login', [
            "head" => ""
        ]);
    }

    public function register(): void
    {
        echo $this->view->render('register', [
            "head" => ""
        ]);
    }

    /**
     * Error handler
     *
     * @param array $data
     * @return void
     */
    public function error(array $data): void
    {
        $errcode = $data['errcode'] ?? "";
        $title = "";
        $description = "";

        switch ($errcode) {
            case "500":
                $title = "Erro Interno";
                $description = "Nosso sistema está apresentando alguma instabilidade. Estamos trabalhando para resolver o problema o mais rápido possível. Agradecemos a compreensão";
                break;
            case "environment":
                $title = "Ambiente";
                $description = "Verifique as variáveis de ambiente do sistema. Apenas com todas as variáveis ajustadas, é possível realizar o acesso ao sistema";
                break;
            case "manutencao":
                $title = "Manutenção";
                $description = "O nosso sistema está passando por uma manutenção. Aguarde e tente novamente mais tarde";
                break;
            default:
                $title = "Página não encontrada";
                $description = "A url que você não foi encontrada em nosso sistema e por isso não pode ser acessada.";
        }

        echo $this->view->render(
            "error",
            [
                "head" => "",
                "title" => $title,
                "description" => $description,
                "active" => ""
            ]
        );
    }
}
