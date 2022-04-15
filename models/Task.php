<?php


class Task
{
    public $id;
    public $descr;
    public $do_from;
    public $do_to;
    public $period_days;
    public $period_qua;
    public $users;

    function __construct($id, $descr, $do_from, $do_to, $period_days, $period_qua){
        $this->id = $id;
        $this->descr = $descr;
        $this->do_from = $do_from;
        $this->do_to = $do_to;
        $this->period_days = $period_days;
        $this->period_qua = $period_qua;
    }

}