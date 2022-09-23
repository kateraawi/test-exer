Ext.define('MyApp.store.UserStore',{
    extend: 'Ext.data.Store',
    model: 'MyApp.model.User',
    autoLoad: true,
    storeId: 'UserStore',
    alias: 'store.userstore',

    proxy: {
        type: 'ajax',
        url: 'http://localhost:80/PHPStormProjects/test-exer/api/api.php?act=UserController&method=getAllUsers',
        reader: {
            type: 'json'
        }
    },
});

//let myStore = Ext.create('Test.store.User');

/*userList.load(function() {
    userList.each(function(record){
        alert(record.get('name'));
    });
});*/

/*Ext.Ajax.request({
    url: 'http://localhost:80/PHPStormProjects/test-exer/services/getAllUsers.php',
    success: function(response, options){
        //alert(response.responseText);
        let objAjax = Ext.decode(response.responseText);
        for (let user in objAjax) {
            userList.add({id: user.id, name: user.name});
        }
        let userFromStore = userList.getAt(0); //получение по индексу в хранилище
        alert(userFromStore.get('name'));
    },
    failure: function(response, options){
        //alert("Ошибка: " + response.statusText);
        return null
    }
});*/