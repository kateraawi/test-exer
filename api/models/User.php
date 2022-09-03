<?php

require_once (__DIR__.'/../dbconfig.php');
require_once ('Task.php');

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
    public $id;

    /** @ORM\Column(type="string") */
    public $name;

    /**
     * @ORM\ManyToMany(targetEntity="Task")
     * @ORM\JoinTable(name="users_tasks")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     */
    private $tasks  = null;

    function __construct(){

        $this->tasks = new ArrayCollection();
    }



    public function getTasks($request = null){
        global $em;

        if($request){
            $this->id = $request['user_id'];
        }

        return $em->find("User", $this->id)->tasks->getValues();
    }

    public function getAll()
    {
        global $em;
        return $em->createQueryBuilder()->select('u')
            ->from('User', 'u')
            ->getQuery()
            ->getResult();

    }

    function addSelf($request = null) {

        global $em;

        if ($request) {
            $this->name = $request['name'];
        }

        $em->persist($this);
        $em->flush();

    }

    function addTask(Task $task)
    {
        global $em;
        $this->tasks[] = $task;
        $em->flush($this);
    }

    function addTaskGroup(Task $task)
    {
        $group = $task->getSelfGroup();
        foreach ($group as $gtask){
            $this->addTask($gtask);
        }
    }

    function unlinkTask(Task $task)
    {
        global $em;
        $this->tasks->removeElement($task);
        $em->flush();
    }

    function unlinkTaskGroup(Task $task)
    {
        $group = $task->getSelfGroup();
        foreach ($group as $gtask){
            $this->unlinkTask($gtask);
        }
    }

    function addTaskViaRequest($request)
    {
        global $em;
        $user = $em->find('User', $request['user_id']);
        $task = $em->find('Task', $request['task_id']);
        $user->addTask($task);

    }

    function addTaskGroupViaRequest($request)
    {
        global $em;
        $user = $em->find('User', $request['user_id']);
        $task = $em->find('Task', $request['task_id']);
        $user->adTaskrGroup($task);
    }

    function unlinkTaskViaRequest($request)
    {
        global $em;
        $user = $em->find('User', $request['user_id']);
        $task = $em->find('Task', $request['task_id']);
        $user->unlinkTask($task);
    }

    function unlinkTaskGroupViaRequest($request)
    {
        global $em;
        $user = $em->find('User', $request['user_id']);
        $task = $em->find('Task', $request['task_id']);
        $user->unlinkTaskGroup($task);
    }

}
