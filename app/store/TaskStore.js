Ext.define('MyApp.store.TaskStore',{
    requires: ['MyApp.config.Globals'],
    extend: 'Ext.data.Store',
    model: 'MyApp.model.Task',
    data:[],
    //autoLoad: true,
    alias: 'store.taskstore',

    proxy: {
        type: 'ajax',
        url: `http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=getUserGridTasks`,
        reader: {
            type: 'json'
        }
    },
});

Ext.define('MyApp.store.CreatedTaskStore',{
    requires: ['MyApp.config.Globals'],
    extend: 'Ext.data.Store',
    model: 'MyApp.model.Task',
    data:[],
    //autoLoad: true,
    alias: 'store.createdtaskstore',

    proxy: {
        type: 'ajax',
        url: `http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=getUserCreatedGridTasks`,
        reader: {
            type: 'json'
        }
    },
});