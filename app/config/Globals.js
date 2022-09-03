Ext.define('MyApp.config.Globals', {
    singleton: true,
    config: {
        userId: null,
    },
    constructor: function (config) {
        this.initConfig(config);
    }
    }
);