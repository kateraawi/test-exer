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



    onLoginClick: function(button) {
        const win = button.up('window');
        const form = win.down('form');
        //const rec = grid.getStore().getAt(rowIndex);
        //const view = Ext.widget('loginform');
        //view.down('form').loadRecord(rec);
        let values = form.getForm().getValues();
        const userId = values.userId;

        MyApp.config.Globals.setUserId(parseInt(userId));
        //console.log(MyApp.config.Globals.userId);
        //win.close();
        Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
        Ext.getCmp('taskListGridId').setTitle(`Задачи пользователя ${MyApp.config.Globals.getUserId()}`);
        //console.log(Ext.getCmp('taskListGridId').title);
        //Ext.getCmp('taskListGridId').doLayout();
        //Ext.getCmp('taskListGridId').refresh();
        //Ext.getCmp('taskListGridId').getStore().load();

        /*Ext.Ajax.request({
            url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=User&method=getTasks',
            method: 'POST',
            params: {
                'userId': userId,
            },
            success: function(response, options){
                Ext.Msg.alert('Успех', 'Успех');

            },
            failure: function(response, options){
                alert("Ошибка: " + response.statusText);
            }
        });*/
    },

    onEditClick: function(grid, rowIndex, colIndex) {
        const rec = grid.getStore().getAt(rowIndex);
        const view = Ext.widget('taskeditform');
        view.down('form').loadRecord(rec);

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
                    'description': values.description,
                    'period_days': values.period_days,
                    'period_quantity': values.period_quantity,
                },
                success: function(response, options){
                    Ext.Msg.alert('Успех', 'Успех');
                },
                failure: function(response, options){
                    alert("Ошибка: " + response.statusText);
                }
            });
        } else {
            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=updateTaskGroup',
                method: 'POST',
                params: {
                    'id': values.id,
                    'description': values.description,
                    'period_days': values.period_days,
                    'period_quantity': values.period_quantity,
                    'group_id': values.group_id,
                },
                success: function(response, options){
                    Ext.Msg.alert('Успех', 'Успех');
                },
                failure: function(response, options){
                    alert("Ошибка: " + response.statusText);
                }
            });
        }

        Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});

    },

    onAddClick: function(button) {
        let view = Ext.widget('taskaddform');
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
                    'description':values.description,
                    'do_from':values.do_from,
                    'do_to':values.do_to,
                    'creator': MyApp.config.Globals.getUserId(),
                },
                success: function(response, options){
                    Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
                    Ext.Msg.alert('Успех', 'Успех');
                },
                failure: function(response, options){
                    alert("Ошибка: " + response.statusText);
                }
            });

        } else {

            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=addTaskGroup',
                method: 'POST',
                params: {
                    'description':values.description,
                    'do_from':values.do_from,
                    'do_to':values.do_to,
                    'period_quantity':values.period_quantity,
                    'period_days':values.period_days,
                    'creator': MyApp.config.Globals.getUserId(),
                },

                success: function(response, options){
                    Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
                    Ext.Msg.alert('Успех', 'Успех');
                },

                failure: function(response, options){
                    alert("Ошибка: " + response.statusText);
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
                    alert("Ошибка: " + response.statusText);
                }
            });

    },

    onDeleteClick: function(grid, rowIndex, colIndex) {
        const rec = grid.getStore().getAt(rowIndex);
        const id = rec.get('id');
        const groupId = rec.get('group_id');
        //alert(groupId);

        if((groupId === 0 && confirm('Вы действительно хотите удалить задачу?')) || (groupId !== 0 && confirm('Это элемент переодической задачи. При удалении не с конца, задача разделится на две другие периодические. Вы действительно хотите его удалить?'))){
            Ext.Ajax.request({
                url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=deleteTask',
                method:'POST',
                params:{
                    'id':rec.get('id')
                },
                success: function(response, options){
                    Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
                },
                failure: function(response, options){
                    alert("Ошибка: " + response.statusText);
                }
            });
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
                    alert("Ошибка: " + response.statusText);
                }
            });
        }
    },

    onCompleteClick: function(checkcolumn, rowIndex, checked, record, eOpts) {
        //let record = grid.getStore().getAt(rowIndex);
        //Ext.Msg.alert('Completed/Uncompleted', 'Completed/Uncompleted ' + record.get('description') + ' (' + record.get('id') + ')');

        Ext.Ajax.request({
            url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=completeTask',
            method:'POST',
            params:{
                'id':record.get('id')
            },
            success: function(response, options){
                Ext.getCmp('taskListGridId').getStore().load({params: {user_id: MyApp.config.Globals.getUserId()}});
            },
            failure: function(response, options){
                alert("Ошибка: " + response.statusText);
            }
        });

    },

    onCellClick: function(sender, record){
        //alert(record.id);
        //let view = Ext.widget('taskview');
        //Ext.getCmp('taskView').setTitle(`Задачи пользователя`);
        Ext.Ajax.request({
            method : "POST",
            url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=getTask&id='+ record.id,
            success : function(response) {
                var obj = response;
                try {
                    obj = Ext.decode(response.responseText);
                } catch (error) {}
                if (obj) {
                    console.log(obj)
                    Ext.create('Ext.window.Window', {
                        title: 'Задача',
                        height: 200,
                        width: 400,
                        layout: 'fit',
                        items: {  // Let's put an empty grid in just to illustrate fit layout
                            xtype: 'grid',
                            border: false,
                            columns: [
                                {
                                    text     : 'id',
                                    flex     : 1,
                                    sortable : true,
                                    dataIndex: 'id'
                                },
                                {
                                    text     : 'description',
                                    width    : 300,
                                    sortable : true,
                                    dataIndex: 'description'
                                }],                 // One header just for show. There's no data,
                            store: new Ext.data.JsonStore({
                                // store configs
                                storeId: 'myStore',
                                autoLoad: true,
                                proxy: {
                                    type: 'ajax',
                                    url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=getTask&id='+ record.id,
                                    reader: {
                                        type: 'json',
                                    }
                                },})

                        }}).show();
                } else {
                    alert("Invalid response")
                }
            },
            failure : function(response) {
                alert("Data request failed");
            }
        });
    }
});