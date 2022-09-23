Ext.define('MyApp.view.front.Main', {
    extend: 'Ext.panel.Panel',
    xtype: 'layout-column',

    requires: [
        'Ext.layout.container.Column',
    ],

    layout: 'column',

    bodyPadding: 5,

    defaults: {
        bodyPadding: 15
    },

    items: [
        {
            title: 'Width = 0.3',
            columnWidth: 0.3,
            items: [
                {xtype:'newMainUserList'}
            ]
        },
        {
            title: 'Width = 0.7',
            columnWidth: 0.7,
            items: [
                {xtype:'newMainTaskList'},
                {xtype: 'MainLoginForm'}
                //{xtype:'form-date'}
            ]
        },
    ]

});

Ext.define('MyApp.view.front.UserList', {
    extend: 'Ext.grid.Panel',
    xtype: 'newMainUserList',
    id:'userListGridId',
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
        delete: {
            iconCls: 'x-fa fa-ban',
            tooltip: 'Удалить',
            text: 'Удалить пользователя',
            handler: 'onDeleteUserClick'
        }
    },

    bbar: [
        '@addUser'
    ],

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
            //getClass: 'getBuyClass',
            //getTip: 'getBuyTip',
            handler: 'onDeleteClick'
        },
        add: {
            iconCls: 'x-fa fa-check',
            tooltip: 'Добавить',
            text: 'Добавить',
            //getClass: 'getBuyClass',
            //getTip: 'getBuyTip',
            handler: 'onAddClick'
        },
    },

    columns : [
            {text: 'id', dataIndex: 'id'},
            {text: 'Группа', dataIndex: 'group_id'},
            {text: 'Описание', dataIndex: 'description', align:'left', flex:1},
            {xtype:'datecolumn', text: 'Начало', dataIndex: 'do_from', format: 'Y-m-d'},
            {xtype:'datecolumn', text: 'Конец', dataIndex: 'do_to', format: 'Y-m-d'},
            {xtype:'checkcolumn', text: 'Статус выполнения', dataIndex: 'completed', listeners: {checkchange: 'onCompleteClick'}},
            {text: 'Создатель', dataIndex: 'creator'},
            {
                menuDisabled: true,
                sortable: false,
                xtype: 'actioncolumn',
                width: 75,
                items: ['@sell', '@buy']
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
            items: [{
                xtype: 'textfield',
                name : 'description',
                fieldLabel: 'Название'
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
            items: [{
                xtype: 'textfield',
                name : 'description',
                fieldLabel: 'Название'
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

Ext.define('MyApp.view.form.Date', {
    extend: 'Ext.container.Container',
    xtype: 'form-date',

    requires: [
        'Ext.panel.Panel',
        'Ext.picker.Date',
        'Ext.picker.Month',
        'Ext.layout.container.VBox',
        'Ext.layout.container.HBox'
    ],

    width: 750,
    layout: {
        type: 'vbox',
        align: 'center'
    },

    items: [{
        xtype: 'container',
        layout: 'hbox',
        items: [{
            title: 'Date Picker (no today)',
            margin: '0 20 0 0',
            items: {
                xtype: 'datepicker',
                showToday: false,
                handler: 'onDatePicked'
            }
        }]
    }]
});
