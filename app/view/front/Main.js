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

    requires: [
        'MyApp.store.UserStore'
    ],

    title: 'Users',
    store: {type: 'userstore'},

    initComponent: function() {
        this.columns = [
            {text: 'id', dataIndex: 'id'},
            {text: 'Name', dataIndex: 'name', flex: 1}
        ];

        this.callParent(arguments);
    }
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

    title: 'Tasks',
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
        complete: {
            iconCls: 'x-fa fa-check',
            tooltip: 'Завершить/Привести в незавершенное состояние',
            //getClass: 'getBuyClass',
            //getTip: 'getBuyTip',
            handler: 'onCompleteClick'
        },
        add: {
            iconCls: 'x-fa fa-check',
            tooltip: 'Добавить',
            text: 'Добавить',
            //getClass: 'getBuyClass',
            //getTip: 'getBuyTip',
            handler: 'onAddClick'
        }
    },

    columns : [
            {text: 'id', dataIndex: 'id'},
            {text: 'Group', dataIndex: 'group_id'},
            {text: 'Description', dataIndex: 'description'},
            {text: 'Do From', dataIndex: 'do_from', flex:1},
            {text: 'Do To', dataIndex: 'do_to', flex:1},
            {text: 'Completed', dataIndex: 'completed'},
            {text: 'Users', dataIndex: 'users'},
            {
                menuDisabled: true,
                sortable: false,
                xtype: 'actioncolumn',
                width: 75,
                items: ['@sell', '@buy', '@complete']
            }
    ],
    bbar: [
        '@add'
    ]
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
