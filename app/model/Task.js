Ext.define('MyApp.model.Task', {
    extend: 'Ext.data.Model',
    alias: 'model.task',
    fields: [
        {name: 'id',  type: 'int'},
        {name: 'name', type: 'string'},
        {name: 'description', type: 'string'},
        {name: 'do_from', type: 'date', dateformat:'Y-m-d'},
        {name: 'do_to', type: 'date', dateformat:'Y-m-d'},
        {name: 'period_days', type: 'int'},
        {name: 'period_quantity', type: 'int'},
        {name: 'completed', type: 'boolean'},
        {name: 'group_id', type: 'int'},
        {name: 'created_at', type: 'date', dateformat:'Y-m-d'},
        {name: 'updated_at', type: 'date', dateformat:'Y-m-d'},
        {name: 'creator'},
        {name: 'repeats'},
        {name: 'users'},
    ],

});