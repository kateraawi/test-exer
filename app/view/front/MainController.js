Ext.define('MyApp.view.front.MainController', {

    extend: 'Ext.app.ViewController',
    alias: 'controller.maincontroller',

    /*getBuyClass: function(v, meta, rec) {
        if (rec.get('change') < 0) {
            return 'array-grid-alert-col';
        } else {
            return 'array-grid-buy-col';
        }
    },

    getBuyTip: function(v, meta, rec) {
        if (rec.get('change') < 0) {
            return 'Hold stock';
        } else {
            return 'Buy stock';
        }
    },*/

    onLoginShowClick: function(button) {
        let view = Ext.widget('loginform');
        //Ext.Msg.alert('Авторизация', 'Теперь для авторизации достаточно нажать на пользователя в списке', Ext.emptyFn);
    },

    onLoginClick: function(button) {
        const win = button.up('window');
        const form = win.down('form');

        let values = form.getForm().getValues();
        const userId = values.userId;

        MyApp.config.Globals.setUserId(parseInt(userId));

        Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
        Ext.getCmp('createdTaskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
        Ext.getCmp('taskListGridId').setTitle(`Задачи пользователя ${MyApp.config.Globals.getUserId()}`);
        win.close();
    },

    onUserSelect: function(sender, record) {
        MyApp.config.Globals.setUserId(parseInt(record.id));
        Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
        Ext.getCmp('createdTaskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
        Ext.getCmp('taskListGridId').setTitle(`Задачи пользователя ${MyApp.config.Globals.getUserId()}`);
    },

    onEditClick: function(grid, rowIndex, colIndex) {
        if(MyApp.config.Globals.getUserId()) {
            const rec = grid.getStore().getAt(rowIndex);
            const view = Ext.widget('taskeditform');
            view.down('form').loadRecord(rec);
        } else {
            Ext.Msg.alert('Ошибка', 'Этой задачи не существует. Для получения списка задач следует авторизоваться.', Ext.emptyFn);
        }
    },

    onSaveEditClick: function(button) {
        const win = button.up('window');
        const form = win.down('form');
        let values = form.getValues();
        const id = form.getRecord().get('id');
        const groupId = form.getRecord().get('group_id');
        values.id = id;
        values.group_id = groupId;
        //alert(groupId);

        if (groupId === 0){
            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=updateTask',
                method: 'POST',
                params: {
                    'id': values.id,
                    'name': values.name,
                    'description': values.description,
                    'period_days': values.period_days,
                    'period_quantity': values.period_quantity,
                },
                success: function(response, options){
                    Ext.Msg.alert('Успех', 'Успех');

                },
                failure: function(response, options){
                    Ext.Msg.alert('Ошибка!',  'Ошибка при изменении задачи!');
                }
            });
        } else {
            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=updateTaskGroup',
                method: 'POST',
                params: {
                    'id': values.id,
                    'name': values.name,
                    'description': values.description,
                    'period_days': values.period_days,
                    'period_quantity': values.period_quantity,
                    'group_id': values.group_id,
                },
                success: function(response, options){
                    Ext.Msg.alert('Успех', 'Успех');
                },
                failure: function(response, options){
                    Ext.Msg.alert('Ошибка!',  'Ошибка при изменении задачи!');
                }
            });
        }

        Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
        Ext.getCmp('createdTaskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});

    },

    onAttachClick: function(grid, rowIndex, colIndex) {
        if(MyApp.config.Globals.getUserId()) {
            const rec = grid.getStore().getAt(rowIndex);
            const view = Ext.widget('attachform');
            view.down('form').loadRecord(rec);
        } else {
            Ext.Msg.alert('Ошибка', 'Этой задачи не существует. Для получения списка задач следует авторизоваться.', Ext.emptyFn);
        }
    },

    onAttachSaveClick: function(button) {
        /*const rec = grid.getStore().getAt(rowIndex);
        const view = Ext.widget('attachform');
        view.down('form').loadRecord(rec);*/

        const win = button.up('window');
        const form = win.down('form');
        let values = form.getValues();
        //const id = form.getRecord().get('id');
        const groupId = form.getRecord().get('group_id');
        const taskId = form.getRecord().get('id');
        //values.id = id;
        //values.user_id = userId;
        //alert(groupId);

        if (groupId === 0){
            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=addUser',
                method: 'GET',
                params: {
                    'task_id': taskId,
                    'user_id': values.userId,
                    //'period_days': values.period_days,
                    //'period_quantity': values.period_quantity,
                },
                success: function(response, options){
                    Ext.Msg.alert('Успех', 'Успех');
                },
                failure: function(response, options){
                    Ext.Msg.alert('Ошибка!',  'Ошибка при прикреплении пользователя!');
                }
            });
        } else {
            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=addUserGroup',
                method: 'GET',
                params: {
                    'task_id': taskId,
                    'user_id': values.userId,
                    //'period_days': values.period_days,
                    //'period_quantity': values.period_quantity,
                },
                success: function(response, options){
                    Ext.Msg.alert('Успех', 'Успех');
                },
                failure: function(response, options){
                    Ext.Msg.alert('Ошибка!',  'Ошибка при прикреплении пользователя!');
                }
            });

            Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
            Ext.getCmp('createdTaskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
    }},

    onAddClick: function(button) {
        if(MyApp.config.Globals.getUserId())
        {
            let view = Ext.widget('taskaddform');
        } else {
            Ext.Msg.alert('Ошибка', 'Для продолжения следует авторизоваться.', Ext.emptyFn);
        }
    },

    onSaveAddClick: function(button) {
        let win    = button.up('window'),
            form   = win.down('form'),
            values = form.getValues();
        //console.log(values);

        
        if(values.period_quantity === '' || parseInt(values.period_quantity) === 1){

            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=addTask',
                method: 'POST',
                params: {
                    'name':values.name,
                    'description':values.description,
                    'do_from':values.do_from,
                    'do_to':values.do_to,
                    'creator': MyApp.config.Globals.getUserId(),
                },
                success: function(response, options){
                    Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
                    Ext.getCmp('createdTaskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
                    Ext.Msg.alert('Успех', 'Успех');
                },
                failure: function(response, options){
                    Ext.Msg.alert('Ошибка!',  'Ошибка при добавлении задачи!');
                }
            });

        } else {

            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=addTaskGroup',
                method: 'POST',
                params: {
                    'name':values.name,
                    'description':values.description,
                    'do_from':values.do_from,
                    'do_to':values.do_to,
                    'period_quantity':values.period_quantity,
                    'period_days':values.period_days,
                    'creator': MyApp.config.Globals.getUserId(),
                },

                success: function(response, options){
                    Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
                    Ext.getCmp('createdTaskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
                    Ext.Msg.alert('Успех', 'Успех');
                },

                failure: function(response, options){
                    Ext.Msg.alert('Ошибка!',  'Ошибка при добавлении задачи!');
                }
            });
        }

    },

    onAddUserClick: function(button) {
        let view = Ext.widget('useraddform');
    },

    onSaveAddUserClick: function(button) {
        let win    = button.up('window'),
            form   = win.down('form'),
            values = form.getValues();

            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=UserController&method=addUser',
                method: 'POST',
                params: {
                    'name':values.name,
                },
                success: function(response, options){
                    Ext.getCmp('userListGridId').getStore().load();
                    Ext.Msg.alert('Успех', 'Успех');
                },
                failure: function(response, options){
                    Ext.Msg.alert('Ошибка!',  'Ошибка при добавлении пользователя!');
                }
            });

    },

    onDeleteClick: function(grid, rowIndex, colIndex) {
        const rec = grid.getStore().getAt(rowIndex);
        const id = rec.get('id');
        const groupId = rec.get('group_id');
        //alert(groupId);

        if(MyApp.config.Globals.getUserId()) {
            if ((groupId === 0 && confirm('Вы действительно хотите удалить задачу?')) || (groupId !== 0 && confirm('Это элемент переодической задачи. При удалении не с конца, задача разделится на две другие периодические. Вы действительно хотите его удалить?'))) {
                Ext.Ajax.request({
                    url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=deleteTask',
                    method: 'POST',
                    params: {
                        'id': rec.get('id')
                    },
                    success: function (response, options) {
                        Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
                        Ext.getCmp('createdTaskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
                    },
                    failure: function (response, options) {
                        Ext.Msg.alert('Ошибка!',  'Ошибка при удалении задачи!');
                    }
                });
            }
        } else {
            Ext.Msg.alert('Ошибка', 'Этой задачи не существует. Для получения списка задач следует авторизоваться.', Ext.emptyFn);
        }
    },

    onDeleteUserClick: function(grid, rowIndex, colIndex) {
        const rec = grid.getStore().getAt(rowIndex);
        const id = rec.get('id');
        const groupId = rec.get('group_id');
        //alert(groupId);

        if(confirm('Вы действительно хотите удалить пользователя?')){
            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=UserController&method=deleteUser',
                method:'POST',
                params:{
                    'id':rec.get('id')
                },
                success: function(response, options){
                    Ext.getCmp('userListGridId').getStore().load();
                },
                failure: function(response, options){
                    Ext.Msg.alert('Ошибка!',  'Ошибка при удалении пользователя!');
                }
            });
        }
    },

    onCompleteClick: function(checkcolumn, rowIndex, checked, record, eOpts) {
        //let record = grid.getStore().getAt(rowIndex);
        //Ext.Msg.alert('Completed/Uncompleted', 'Completed/Uncompleted ' + record.get('description') + ' (' + record.get('id') + ')');
        if(MyApp.config.Globals.getUserId()) {
            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=completeTask',
                method: 'POST',
                params: {
                    'id': record.get('id')
                },
                success: function (response, options) {
                    Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
                    Ext.getCmp('createdTaskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
                },
                failure: function (response, options) {
                    Ext.Msg.alert('Ошибка!',  'Ошибка при завершении задачи!');
                }
            });
        } else {
            this.onLoginShowClick();
        }
    },

    onCellClick: function(sender, record){
        Ext.Ajax.request({
            method : "POST",
            url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=getTask&id='+ record.id,
            success : function(response) {
                var obj = response;
                try {
                    obj = Ext.decode(response.responseText);
                } catch (error) {}
                if (obj) {
                    let taskStore = new Ext.data.JsonStore({
                        storeId: 'myTaskStore',
                        data: [obj],
                        //autoLoad: true,
                        /*proxy: {
                            type: 'ajax',
                            //url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=getTask&id='+ record.id,
                            reader: {
                                type: 'json',
                            }
                            },*/
                    })
                    let userStore = new Ext.data.JsonStore({
                        storeId: 'myUserStore',
                        data: obj.users,
                        proxy: {
                            type: 'ajax',
                            reader: {
                                type: 'json',
                            }
                        },});
                    let creatorStore = new Ext.data.JsonStore({
                        storeId: 'myUserStore',
                        data: [obj.creator],
                        proxy: {
                            type: 'ajax',
                            reader: {
                                type: 'json',
                            }
                        },});
                    //console.log(obj)
                    Ext.create('Ext.window.Window', {
                        title: `Задача #${obj.id}`,
                        height: 500,
                        width: 400,
                        layout: {
                        type: 'vbox',
                        align : 'stretch',
                            pack  : 'start',
                        },
                        items: [
                            {
                                xtype: 'panel',
                                border: true,
                                title: 'Сведения',
                                html:`
                                    <div style="font-size: 14px; padding: 10px">
                                        Добавлен: ${obj.creator.name} <br>
                                        в ${obj.created_at} <br>
                                        Последнее редактирование: ${obj.updated_at} <br>
                                        Выполнить в срок: ${obj.do_from} – ${obj.do_to} <br>
                                        Статус: ${obj.completed === 1 ? 'Выполнено' : 'Не выполнено'} <br><br>
                                        Тема: ${obj.name} <br>
                                        Описание: <p> ${obj.description} </p>
                                    </div>
                                `,
                            },
                            {
                                xtype: 'grid',
                                border: true,
                                title: 'Назначенные пользователи',
                                columns: [
                                    {
                                        text     : 'id',
                                        flex     : 1,
                                        sortable : true,
                                        dataIndex: 'id'
                                    },
                                    {
                                        text     : 'Имя',
                                        align: 'left',
                                        width    : 300,
                                        sortable : true,
                                        dataIndex: 'name'
                                    }],
                                store: userStore
                            },

                        ]
                    }).show();
                } else {
                    Ext.Msg.alert('Ошибка!',  'Ошибка при получении задачи!');
                }
            },
            failure : function(response) {
                Ext.Msg.alert('Ошибка!',  'Ошибка при получении задачи!');
            }
        });
    }
});