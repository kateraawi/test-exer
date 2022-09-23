<?php
namespace TestExer\Model;

require_once (__DIR__.'/../vendor/autoload.php');
require_once (__DIR__.'/../dto/TaskDto.php');
require_once (__DIR__.'/../repository/TaskRepository.php');
require_once ('User.php');

use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use TestExer\Repository;
use TestExer\dto\TaskDto as TaskDto;

/**
 * @ORM\Entity(repositoryClass="TestExer\Repository\TaskRepository")
 * @ORM\Table(name="tasks")
 */
class Task
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    public $id;

    /**
     * @ORM\Column(name="`descr`", type="string")
     */
    public $description;


    /**
     * @ORM\Column(type="string")
     */
    public $do_from;

    /**
     * @ORM\Column(type="string")
     */
    public $do_to;

    /**
     * @ORM\Column(type="integer")
     */
    public $period_days;

    /** @ORM\Column(name="period_qua", type="integer") */
    public $period_quantity;

    /** @ORM\Column(type="integer") */
    public $completed;

    /**
     * @ORM\Column(type="integer")
     */
    public $group_id;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="users_tasks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    public $users  = null;

    //public $repeats;

    /**
     * @ORM\Column(type="string")
     */
    public $created_at;

    /**
     * @ORM\Column(type="string")
     */
    public $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="created")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id")
     */
    public $creator;

    function __construct()
    {
        $this->users = new ArrayCollection();
    }

    function setDescription($description)
    {
        $this->description = $description;
    }

    function setPeriod($do_from, $do_to, $period_days, $period_quantity)
    {
        $this->do_from = $do_from;
        $this->do_to = $do_to;
        $this->period_days = $period_days;
        $this->period_quantity = $period_quantity;
    }

    function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    function setGroupId($group_id)
    {
        $this->group_id = $group_id;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function getRepeatedPeriods()
    {

        $do_from = $this->do_from;
        $do_to = $this->do_to;

        $repeats = [];

        $do_for = ((new DateTime())->setTimestamp(0)->add(date_diff(new DateTime($do_from), new DateTime($do_to)))->getTimestamp())/86400;

        for ($i = 0; $i < $this->period_quantity; $i++) {
            array_push($repeats,
                [$do_from, $do_to]
            );

            $do_from = date('Y-m-d', strtotime($do_to . "+$this->period_days days"));
            $do_to = date('Y-m-d', strtotime($do_from . "+$do_for days"));
        }

        return $repeats;
    }

    function getNewGroupId()
    {
        global $em;

        return $em->createQueryBuilder()->select('MAX(t.group_id)')
                ->from(Task::class, 't')
                ->getQuery()
                ->getSingleScalarResult() + 1;
    }

    public function getSelfGroup()
    {
        global $em;
        return $em->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't')
            ->where("t.group_id = $this->group_id")
            ->getQuery()
            ->getResult();
    }

    function addUser(User $user)
    {
        global $em;
        $this->users[] = $user;
        $em->persist($this);
        $em->flush($this);
    }

    function addUserGroup(User $user)
    {
        $group = $this->getSelfGroup();
        foreach ($group as $task){
            $task->addUser($user);
        }
    }

    function unlinkUser(User $user)
    {
        global $em;
        $this->users->removeElement($user);
        $em->flush();
    }

    function unlinkUserGroup(User $user)
    {
        $group = $this->getSelfGroup();
        foreach ($group as $task){
            $task->unlinkUser($user);
        }
    }

    function toDto()
    {
        $dto = new TaskDto();
        return  $dto->toDto($this);
    }
}
