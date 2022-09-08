<?php

class TaskDto
{
    public $id;

    public $description;

    public $do_from;

    public $do_to;

    public $period_days;

    public $period_quantity;

    public $completed;

    public $group_id;

    public $users;

    function toDto(Task $task)
    {

        $this->id = $task->id;
        $this->description = $task->description;
        $this->do_from = $task->do_from;
        $this->do_to = $task->do_to;
        $this->period_days = $task->period_days;
        $this->completed = $task->completed;
        $this->group_id = $task->group_id;
        $this->users = $task->users;

        return $this;
    }
}