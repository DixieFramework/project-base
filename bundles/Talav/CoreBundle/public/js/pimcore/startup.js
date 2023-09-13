pimcore.registerNS("pimcore.plugin.TalavCoreBundle");

pimcore.plugin.TalavCoreBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("TalavCoreBundle ready!");
    }
});

var TalavCoreBundlePlugin = new pimcore.plugin.TalavCoreBundle();
