pimcore.registerNS("pimcore.plugin.TalavGalleryBundle");

pimcore.plugin.TalavGalleryBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("TalavGalleryBundle ready!");
    }
});

var TalavGalleryBundlePlugin = new pimcore.plugin.TalavGalleryBundle();
