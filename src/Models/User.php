<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * @package Source\Models
 */
class User extends Model
{
    /**
     * User Constructor
     */
    public function __construct()
    {
        parent::__construct(
            "users",
            [],
            ["full_name", "user_name", "password", "role"]
        );
    }

    /**
     * @param string $userName
     * @return User|null
     */
    public function findOneByUser(string $userName): ?User
    {
        return $this->find(
            "user_name = :u",
            "u={$userName}",
        )->fetch();
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->required()) {
            $this->message->warning("The fields full name, user name and password are mandatory.");
            return false;
        }

        if (!is_passwd($this->password)) {
            $min = CONF_PASSWD_MIN_LEN;
            $max = CONF_PASSWD_MAX_LEN;
            $this->message->warning("The password must have beetween {$min} and {$max} characters");
            return false;
        } else {
            $this->password = passwd($this->password);
        }

        /** User Update */
        if (!empty($this->id)) {
            $userId = $this->id;

            if ($this->find("user_name = :u AND id != :i", "u={$this->user_name}&i={$userId}", "id")->fetch()) {
                $this->message->warning("The user name informed is already in use");
                return false;
            }

            $this->update($this->safe(), "id = :id", "id={$userId}");
            if ($this->fail()) {
                $this->message->error("There was an error updating");
                return false;
            }
        }

        /** User Create */
        if (empty($this->id)) {
            if ($this->findOneByUser($this->user_name, "id")) {
                $this->message->warning("The user name informed is already in use");
                return false;
            }

            $userId = $this->create($this->safe());
            if ($this->fail()) {
                $this->message->error("There was an error creating the user");
                return false;
            }
        }

        $this->data = ($this->findOneById($userId))->data();
        return true;
    }
}
