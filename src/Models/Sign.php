<?php

namespace Source\Models;

use Source\Support\Message;
use stdClass;

class Sign
{
    /** @var Message $message */
    protected Message $message;

    /** @var string $user */
    protected string $user;

    /** @var string $password */
    protected string $password;

    /**
     * Login constructor
     *
     * @param string $user
     * @param string $password
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->message = new Message();
    }

    /**
     * @return stdClass|null
     */
    public function login(): ?stdClass
    {
        if (!is_passwd($this->password)) {
            $this->message->error("The password informed is not valid");
            return null;
        }

        $user = (new User())->findOneByUser($this->user);
        if (empty($user)) {
            $this->message->error("The user informed was not found");
            return null;
        }

        if (!passwd_verify($this->password, $user->password)) {
            $this->message->error("The password is incorrect. Try it again");
            return null;
        }

        if (passwd_rehash($user->password)) {
            $user->password = $this->password;
            $user->save();
        }

        return (object) [
            "token" => Token::token($user),
            "user" => (object)[
                "id" => $user->id,
                "user_name" => $user->user_name,
            ]
        ];
    }

    public function message(): Message
    {
        return $this->message;
    }
}
