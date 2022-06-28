SELECT * FROM tasks INNER JOIN users_tasks ON tasks.id = users_tasks.task_id AND tasks.id = ? INNER JOIN users ON users.id = users_tasks.user_id
