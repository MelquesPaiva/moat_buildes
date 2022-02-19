<?php

namespace Source\Core;

use Exception;
use PDO;
use PDOException;

/**
 * Esta classe é responsável pela conexão do sistema com o banco dados
 * A conexão é feita utilizando a biblioteca PDO
 * @package Source\Core
 */
class Connect
{
    protected Exception $fail;

    /**
     * Constante com um array com algumas opções para a conexõa com o banco de dados
     * dentre elas a codificação de caracteres UFT-8, a formatação do retorno de dados
     * retornando sempre objetos, e o lançamento de excessões sempre que houver erro     
     */
    private const OPTIONS = [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ];

    /**
     * Variável recebe uma instância de conexão de banco de dados, que pode ser reaproveitada
     * durante toda a execução do sistema, por conta de ser uma propriedade estática
     */
    private static PDO $instance;

    /**
     * Função retorna a instância atual de conexão com o banco de dados. Caso não exista nenhuma
     * instância, ele realiza a conexão utilizando o PDO. Os parâmetros do banco de dados como
     * o HOST, nome do banco de dados (dbname), usuário e senha, estão salvos no arquivo com variáveis
     * de ambiente chamado .env, que se encontra na raiz do projeto
     */
    public static function getInstance(): ?PDO
    {
        if (empty(self::$instance)) {
            try {
                self::$instance = new \PDO(
                    "mysql:host=127.0.0.1;dbname=moat_task",
                    "root",
                    "",
                    self::OPTIONS
                );
            } catch (PDOException $ex) {
                self::$fail = $ex;
            }
        }

        return self::$instance;
    }

    /** 
     * Construtor da classe, por meio da palavra reserverda final, impede que essa
     * classe seja extendida e por sua vez modificada por classes filhas
     */
    final private function __construct()
    {
    }

    /**
     * Impedindo a clonagem de instâncias desta classe'
     */
    final private function __clone()
    {
    }
}
