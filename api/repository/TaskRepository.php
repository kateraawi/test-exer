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
            ->select('t.id, t.do_from, t.do_to', 't.completed', 't.group_id', 't.created_at', 't.updated_at',
            't.period_days', 't.period_quantity')
            ->from(Task::class, 't')
            ->innerJoin('t.users', 'u', 'WITH', 'u.id = :user_id')
            ->setParameter('user_id', $request['user_id']);
        return $q->getQuery()->getResult();

    }


}