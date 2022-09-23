<?php

namespace TestExer\dto;

use TestExer\Model\User as User;

class UserDto
{
    public $id;

    public $name;

    public $tasks;

    function toDto(User $user)
    {

        $this->id = $user->id;
        $this->name = $user->name;
        $this->users = $user->tasks;

        return $this;
    }
}