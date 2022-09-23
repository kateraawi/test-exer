Ext.define('MyApp.store.TaskStore',{
    requires: ['MyApp.config.Globals'],
    extend: 'Ext.data.Store',
    model: 'MyApp.model.Task',
    autoLoad: true,
    alias: 'store.taskstore',

    proxy: {
        type: 'ajax',
        url: `http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=TaskController&method=getUserGridTasks`,
        reader: {
            type: 'json'
        }
    },
});