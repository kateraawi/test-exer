<?php

namespace TestExer\Repository;
use Doctrine\ORM\EntityRepository;
use TestExer\Model\User;
use TestExer\Model\Task;

class TaskRepository extends EntityRepository
{
    public function getUserGridTasks($request)
    {

        $q = $this->_em->createQueryBuilder()
            ->select('t.id, t.name, t.do_from, t.do_to', 't.completed', 't.group_id', 't.created_at', 't.updated_at',
            't.period_days', 't.period_quantity')
            ->from(Task::class, 't')
            ->innerJoin('t.users', 'u', 'WITH', 'u.id = :user_id')
            ->setParameter('user_id', $request['user_id']);
        return $q->getQuery()->getResult();

    }

    public function getUserCreatedGridTasks($request)
    {
        $q = $this->_em->createQueryBuilder()
            ->select('t.id, t.name, t.do_from, t.do_to', 't.completed', 't.group_id', 't.created_at', 't.updated_at',
                't.period_days', 't.period_quantity')
            ->from(Task::class, 't')
            ->where('t.creator = :user_id')
            ->setParameter('user_id', $request['user_id']);
        return $q->getQuery()->getResult();

    }

    public function getGroupRepeats($request)
    {
        $q = $this->_em->createQueryBuilder()
            ->select('t.id, t.do_from, t.do_to', 't.completed')
            ->from(Task::class, 't')
            ->where('t.group_id = :group_id')
            ->setParameter('group_id', $request['id']);
        //print_r($q->getQuery()->getResult());
        return $q->getQuery()->getResult();
    }

}