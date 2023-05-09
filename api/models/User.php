<?php
namespace TestExer\Model;

require_once (__DIR__.'/../dbconfig.php');
require_once (__DIR__.'/../dto/UserDto.php');
require_once ('Task.php');

namespace TestExer\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TestExer\dto\UserDto as UserDto;

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
    public $tasks = null;

    /**
     * One product has many features. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Task", mappedBy="creator")
     */
    public $created;

    function __construct(){

        $this->tasks = new ArrayCollection();
        $this->created = new ArrayCollection();
    }

    public function getTasks($request = null){
        global $em;

        if($request){
            $this->id = $request['user_id'];
        }
        $tasks = $em->find("User", $this->id)->tasks->getValues();
        return $tasks;
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

    function toDto()
    {
        $dto = new UserDto();
        return  $dto->toDto($this);
    }

}
