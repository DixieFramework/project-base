pimcore.registerNS("pimcore.plugin.TalavImageBundle");

pimcore.plugin.TalavImageBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("TalavImageBundle ready!");
    }
});

var TalavImageBundlePlugin = new pimcore.plugin.TalavImageBundle();
