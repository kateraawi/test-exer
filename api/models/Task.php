<?php

require_once (__DIR__.'/../dbconfig.php');
require_once (__DIR__.'/../vendor/autoload.php');
require_once ('User.php');

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
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
    private $users  = null;

    //public $repeats;

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

    public function getUsers($request = null)
    {
        global $em;

        if($request){
            $this->id = $request['task_id'];
        }

        return $em->find("Task", $this->id)->users->getValues();
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

    private function getNewGroupId()
    {
        global $em;

        return $em->createQueryBuilder()->select('MAX(t.group_id)')
                ->from('Task', 't')
                ->getQuery()
                ->getSingleScalarResult() + 1;
    }

    public function getSelfGroup()
    {
        global $em;

        return $em->createQueryBuilder()
            ->select('t')
            ->from('Task', 't')
            ->where("t.group_id = $this->group_id")
            ->getQuery()
            ->getResult();
    }

    public function getAll()
    {
        global $em;
        return $em->createQueryBuilder()->select('t')
            ->from('Task', 't')
            ->getQuery()
            ->getResult();

    }

    function addSelf($request = null)
    {
        global $em;

        if ($request) {
            $this->description = $request['description'];
            $this->do_from = date('Y-m-d', strtotime($request['do_from']));
            $this->do_to = date('Y-m-d', strtotime($request['do_to']));
            $this->period_days = 0;
            $this->period_quantity = 0;
            $this->completed = 0;
        }

        $em->persist($this);
        $em->flush();

    }

    function addSelfGroup($request = null) {

        if ($request) {
            $this->description = $request['description'];
            $this->do_from = date('Y-m-d', strtotime($request['do_from']));
            $this->do_to = date('Y-m-d', strtotime($request['do_to']));
            $this->period_days = (int)$request['period_days'];
            $this->period_quantity = (int)$request['period_quantity'];
        }

        $group_id = $this->getNewGroupId();

        foreach ($this->getRepeatedPeriods() as $period){

            $task = new Task();
            $task->setDescription($this->description);
            $task->setPeriod($period[0], $period[1], $this->period_days, $this->period_quantity);
            $task->setCompleted(0);
            $task->setGroupId($group_id);

            $task->addSelf();
        }

    }

    function updateSelf($request = null)
    {

        global $em;

        if ($request) {
            $this->id = $request['id'];
            $this->description = $request['description'];
        }

        $thisTask = $em->find('Task', $this->id);
        $thisTask->setDescription($this->description);
        $em->persist($thisTask);
        $em->flush();

    }

    public function updateSelfGroup($request = null)
    {

        global $em;

        if ($request) {
            $this->id = $request['id'];
            $this->description = $request['description'];
            $this->period_quantity = $request['period_quantity'];
        }

        $thisTask = $em->find('Task', $this->id);
        $group = $thisTask->getSelfGroup();
        $newInGroup = [];

        if ($this->period_quantity < $thisTask->period_quantity) {
            $delArr = array_slice($group, $this->period_quantity - $thisTask->period_quantity);
            $group = array_slice($group, 0, $this->period_quantity);
            foreach ($delArr as $delTask){
                $em->remove($delTask);
            }
        }
        if ($this->period_quantity > $thisTask->period_quantity) {
            for ($i = 0; $i < $this->period_quantity - $thisTask->period_quantity; $i++) {
                $task = new Task();
                $task->setDescription($this->description);
                $task->setCompleted(0);
                $task->setGroupId($thisTask->group_id);
                array_push($group, $task);
                array_push($newInGroup, $task);
            }
        }
        $group[0]->setPeriod($group[0]->do_from, $group[0]->do_to, $group[0]->period_days, count($group));
        $periods = $group[0]->getRepeatedPeriods();

        foreach ($group as $elementKey => $task){
            $task->setDescription($this->description);
            $task->setPeriod($periods[$elementKey][0], $periods[$elementKey][1], $group[0]->period_days, count($group));
            $em->persist($task);

        }

        foreach ($newInGroup as $elementKey => $task) {
            foreach ($group[0]->getUsers() as $user) {
                $task->addUser($user);
            }
        }

        $em->flush();
        return $group;

    }

    function completeSelf($request = null)
    {
        global $em;

        if ($request) {
            $this->id = $request['id'];
        }

        $task=$em->find('Task', $this->id);
        $task->setCompleted(1);
        $em->persist($task);
        $em->flush();

    }

    function deleteSelf($request = null)
    {
        global $em;

        if ($request) {
            $this->id = (int)$request['id'];
        }

        $thisTask = $em->find('Task', $this->id);

        if ($thisTask->group_id !== null){
            $group = $thisTask->getSelfGroup();

            if ($thisTask->id !== $group[0]->id){

                $foundPositionInGroup = false;
                $oldGroup = [];

                foreach ($group as $elementKey => $task) {

                    if (!$foundPositionInGroup) {
                        if ($task->id !== $thisTask->id) $oldGroup[] = $task;
                        unset($group[$elementKey]);
                    }

                    if ($task->id === $thisTask->id) {
                        $foundPositionInGroup = true;
                    }

                }

                $em->remove($thisTask);
                $groupId = $this->getNewGroupId();

                foreach ($group as $elementKey => $task) {
                    $task->setGroupId($groupId);
                    $task->setPeriod($task->do_from, $task->do_to, $task->period_days, count($group));
                    $em->persist($task);
                }

                foreach ($oldGroup as $elementKey => $task) {
                    $task->setPeriod($task->do_from, $task->do_to, $task->period_days, count($oldGroup));
                    $em->persist($task);
                }

            } else {
                $em->remove($thisTask);
            }
            $em->flush();
        }

        return [$oldGroup, $group];

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

    function addUserViaRequest($request)
    {
        global $em;
        $user = $em->find('User', $request['user_id']);
        $task = $em->find('Task', $request['task_id']);
        $task->addUser($user);

    }

    function addUserGroupViaRequest($request)
    {
        global $em;
        $user = $em->find('User', $request['user_id']);
        $task = $em->find('Task', $request['task_id']);
        $task->addUserGroup($user);
    }

    function unlinkUserViaRequest($request)
    {
        global $em;
        $user = $em->find('User', $request['user_id']);
        $task = $em->find('Task', $request['task_id']);
        $task->unlinkUser($user);
    }

    function unlinkUserGroupViaRequest($request)
    {
        global $em;
        $user = $em->find('User', $request['user_id']);
        $task = $em->find('Task', $request['task_id']);
        $task->unlinkUserGroup($user);
    }
}
