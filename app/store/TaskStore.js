Ext.define('MyApp.store.TaskStore',{
    requires: ['MyApp.config.Globals'],
    extend: 'Ext.data.Store',
    model: 'MyApp.model.Task',
    autoLoad: false,
    alias: 'store.taskstore',

    proxy: {
        type: 'ajax',
        url: `http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=User&method=getTasks`,
        reader: {
            type: 'json'
        }
    },
});