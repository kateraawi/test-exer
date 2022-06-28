SELECT * FROM users INNER JOIN users_tasks ON users.id = users_tasks.user_id AND users.id = ?  INNER JOIN tasks ON tasks.id = users_tasks.task_id
