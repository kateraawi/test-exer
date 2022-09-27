Ext.define('MyApp.view.front.Main', {
    extend: 'Ext.panel.Panel',
    xtype: 'layout-column',
    autoScroll: true,
    requires: [
        'Ext.layout.container.Column',
    ],

    layout: 'column',

    items: [
        {
            title: 'Список дел',
            columnWidth: 1,
            bodyPadding: 0,
            defaults: {
                bodyPadding: 15
            },

            requires: [
                'Ext.layout.container.Column',
            ],

            layout: 'column',
            items:
            [
                {
                    columnWidth: 0.4,
                    items: [
                        {xtype:'newMainUserList'},
                        {xtype:'newMainCreatedTaskList'},
                    ]
                },
                {
                    columnWidth: 0.6,
                    items: [
                        {xtype:'newMainTaskList'},
                        //{xtype: 'MainLoginForm'}
                        //{xtype:'form-date'}
                    ]
                }
            ]
        }
    ]

});

Ext.define('MyApp.view.front.UserList', {
    extend: 'Ext.grid.Panel',
    xtype: 'newMainUserList',
    id:'userListGridId',
    height:300,
    requires: [
        'MyApp.store.UserStore',
        'MyApp.view.front.MainController'
    ],

    controller: 'maincontroller',

    title: 'Пользователи',
    store: {type: 'userstore'},

    columns: [
        {text: 'id', dataIndex: 'id'},
        {text: 'Имя', dataIndex: 'name', flex: 1},
        {
            menuDisabled: true,
            sortable: false,
            xtype: 'actioncolumn',
            items: ['@delete']
        }
    ],

    actions: {
        addUser: {
            iconCls: 'x-fa fa-plus',
            tooltip: 'Добавить',
            text: 'Добавить пользователя',
            handler: 'onAddUserClick'
        },
        loginUser: {
            iconCls: 'x-fa fa-user',
            tooltip: 'Открыть форму входа',
            text: 'Открыть форму входа',
            handler: 'onLoginShowClick'
        },
        delete: {
            iconCls: 'x-fa fa-ban',
            tooltip: 'Удалить',
            text: 'Удалить пользователя',
            handler: 'onDeleteUserClick'
        }
    },

    bbar: [
        '@addUser', '@loginUser'
    ],

    listeners: {
        select: 'onUserSelect'
    },

});

Ext.define('MyApp.view.front.TaskList', {
    extend: 'Ext.grid.Panel',
    xtype: 'newMainTaskList',

    controller: 'maincontroller',

    requires: [
        'MyApp.store.TaskStore',
        'Ext.grid.column.Action',
        'MyApp.view.front.MainController'
    ],

    title: 'Задачи',
    id:'taskListGridId',
    store: {type: 'taskstore'},
    height: 750,

    actions: {
        sell: {
            iconCls: 'x-fa fa-cog',
            tooltip: 'Изменить',
            handler: 'onEditClick'
        },
        buy: {
            iconCls: 'x-fa fa-ban',
            tooltip: 'Удалить',
            handler: 'onDeleteClick'
        },
        add: {
            iconCls: 'x-fa fa-plus',
            tooltip: 'Добавить задачу',
            text: 'Добавить задачу',
            handler: 'onAddClick'
        },
        user: {
            iconCls: 'x-fa fa-user',
            tooltip: 'Добавить пользователя',
            handler: 'onAttachClick'
        },
    },

    columns : [
            {text: 'id', dataIndex: 'id'},
            {text: 'Группа', dataIndex: 'group_id'},
            {text: 'Имя', dataIndex: 'name', align:'left', flex:1},
            {xtype:'datecolumn', text: 'Начало', dataIndex: 'do_from', format: 'Y-m-d'},
            {xtype:'datecolumn', text: 'Конец', dataIndex: 'do_to', format: 'Y-m-d'},
            {xtype:'checkcolumn', text: 'Статус выполнения', dataIndex: 'completed', listeners: {checkchange: 'onCompleteClick'}},
            {xtype:'datecolumn', text: 'Создано', dataIndex: 'created_at', format: 'Y-m-d'},
            {xtype:'datecolumn', text: 'Обновлено', dataIndex: 'updated_at', format: 'Y-m-d'},

    ],

    listeners: {
        select: 'onCellClick'
    },
});

Ext.define('MyApp.view.front.CreatedTaskList', {
    extend: 'Ext.grid.Panel',
    xtype: 'newMainCreatedTaskList',

    height: 450,
    controller: 'maincontroller',

    requires: [
        'MyApp.store.CreatedTaskStore',
        'Ext.grid.column.Action',
        'MyApp.view.front.MainController'
    ],

    title: 'Созданные задачи',
    id:'createdTaskListGridId',
    store: {type: 'createdtaskstore'},

    actions: {
        sell: {
            iconCls: 'x-fa fa-cog',
            tooltip: 'Изменить',
            handler: 'onEditClick'
        },
        buy: {
            iconCls: 'x-fa fa-ban',
            tooltip: 'Удалить',
            handler: 'onDeleteClick'
        },
        add: {
            iconCls: 'x-fa fa-plus',
            tooltip: 'Добавить задачу',
            text: 'Добавить задачу',
            handler: 'onAddClick'
        },
        user: {
            iconCls: 'x-fa fa-user',
            tooltip: 'Добавить пользователя',
            handler: 'onAttachClick'
        },
    },

    columns : [
        {text: 'id', dataIndex: 'id', width: 40},
        {text: 'Группа', dataIndex: 'group_id', width: 60},
        {text: 'Имя', dataIndex: 'name', align:'left', flex:1},
        {xtype:'datecolumn', text: 'Начало', dataIndex: 'do_from', format: 'Y-m-d'},
        {xtype:'datecolumn', text: 'Конец', dataIndex: 'do_to', format: 'Y-m-d'},
        {xtype:'checkcolumn', text: 'Статус выполнения', dataIndex: 'completed', width: 20, listeners: {checkchange: 'onCompleteClick'}},
        {
            menuDisabled: true,
            sortable: false,
            xtype: 'actioncolumn',
            width: 75,
            items: ['@sell', '@buy', '@user']
        }
    ],
    bbar: [
        '@add'
    ],
    listeners: {
        select: 'onCellClick'
    },
});

Ext.define('MyApp.view.front.LoginForm', {
    xtype: 'MainLoginForm',

    extend: 'Ext.window.Window',
    alias: 'widget.loginform',
    controller: 'maincontroller',
    requires: ['MyApp.view.front.MainController'],
    title: 'Логин',
    layout: 'fit',
    autoShow: true,

    initComponent: function() {
        this.items = [{
            xtype: 'form',
            items: [{
                xtype: 'numberfield',
                name: 'userId',
                fieldLabel: 'ID'
                },
                {
                    xtype: 'button',
                    text: 'Вход',
                    handler: 'onLoginClick'
                }
            ],
        }];

        this.callParent(arguments);
    }
});

Ext.define('MyApp.view.front.AttachForm', {
    xtype: 'MainAttachForm',

    extend: 'Ext.window.Window',
    alias: 'widget.attachform',
    controller: 'maincontroller',
    requires: ['MyApp.view.front.MainController'],
    title: 'Прикрепление пользователя к задаче',
    layout: 'fit',
    autoShow: true,

    initComponent: function() {
        this.items = [{
            xtype: 'form',
            items: [{
                xtype: 'numberfield',
                name: 'userId',
                fieldLabel: 'ID'
            },
                {
                    xtype: 'button',
                    text: 'Прикрепить',
                    handler: 'onAttachSaveClick'
                }
            ],
        }];

        this.callParent(arguments);
    }
});

Ext.define('MyApp.view.front.TaskForm', {
    extend: 'Ext.window.Window',
    alias: 'widget.taskeditform',
    controller: 'maincontroller',
    requires: ['MyApp.view.front.MainController'],
    title: 'Задание',
    layout: 'fit',
    autoShow: true,

    initComponent: function() {
        this.items = [{
            xtype: 'form',
            items: [
                {
                    xtype: 'textfield',
                    name : 'name',
                    fieldLabel: 'Название'
                },
                {
                xtype: 'textfield',
                name : 'description',
                fieldLabel: 'Описание'
            }
            ,{
                xtype: 'numberfield',
                name : 'period_quantity',
                fieldLabel: 'Количество перерывов (нельзя изменить на непериодической задаче)',
                minValue: 1,
            },
            {
                xtype: 'button',
                text: 'Сохранить',
                handler: 'onSaveEditClick'
            }
            ],
        }];

        this.callParent(arguments);
    }
});

Ext.define('MyApp.view.front.TaskAddForm', {
    extend: 'Ext.window.Window',
    alias: 'widget.taskaddform',
    id:'taskView',
    controller: 'maincontroller',
    requires: ['MyApp.view.front.MainController'],
    title: 'Задание',
    layout: 'fit',
    autoShow: true,

    initComponent: function() {
        this.items = [{
            xtype: 'form',
            items: [
                {
                    xtype: 'textfield',
                    name : 'name',
                    fieldLabel: 'Название'
                },
                {xtype: 'textfield',
                name : 'description',
                fieldLabel: 'Описание'
                },
                {
                    xtype: 'textfield',
                    name : 'do_from',
                    fieldLabel: 'От'
                },
                {
                    xtype: 'textfield',
                    name : 'do_to',
                    fieldLabel: 'До'
                },
                {
                    xtype: 'numberfield',
                    name : 'period_days',
                    fieldLabel: 'Дней между перерывами',
                    minValue: 0,
                }
                ,{
                    xtype: 'numberfield',
                    name : 'period_quantity',
                    fieldLabel: 'Количество перерывов',
                    minValue: 1,
                },
                {
                    xtype: 'button',
                    text: 'Сохранить',
                    handler: 'onSaveAddClick'
                }
            ],
        }];

        this.callParent(arguments);
    }
});

Ext.define('MyApp.view.front.UserAddForm', {
    extend: 'Ext.window.Window',
    alias: 'widget.useraddform',
    controller: 'maincontroller',
    requires: ['MyApp.view.front.MainController'],
    title: 'Пользователь',
    layout: 'fit',
    autoShow: true,

    initComponent: function() {
        this.items = [{
            xtype: 'form',
            items: [{
                xtype: 'textfield',
                name : 'name',
                fieldLabel: 'Имя'
            },
                {
                    xtype: 'button',
                    text: 'Сохранить',
                    handler: 'onSaveAddUserClick'
                }
            ],
        }];

        this.callParent(arguments);
    }
});

Ext.define('MyApp.view.front.TaskView', {
    extend: 'Ext.window.Window',
    alias: 'widget.taskview',
    controller: 'maincontroller',
    requires: ['MyApp.view.front.MainController'],
    title: 'Задача',
    layout: 'fit',
    autoShow: true,

    initComponent: function() {
        this.items = [{
            xtype: 'form',
            items: [{
                xtype: 'textfield',
                name : 'name',
                fieldLabel: 'Имя'
            },
            ],
        }];

        this.callParent(arguments);
    }
});
