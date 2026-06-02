define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Amasty_PromoBanners/js/injector',
    'catalogAddToCart'
], function ($, customerData, bannerInjector) {
    'use strict';
    var banner = {
            data: {
                bannersData: {
                    banners: [],
                    content: [],
                    injectorParams: null,
                    injectorSectionId: '',
                    sections: []
                },
                requestUrl: '',
                categoryId: null,
                productId: null,
            },
            selectors: {
                BANNER_SELECTOR: '.ambanners.ambanner-'
            },

            init: function (firstLoad = true) {
                this.insertBanners(firstLoad);
                this.injectBanners();
                $("[data-role=amasty-banner-tocart-form]").catalogAddToCart({});
            },

            getSectionBanners: function (section) {
                if (!(section in this.data.bannersData.sections)) {
                    return [];
                }

                return this.data.bannersData.sections[section].map(function (id) {

                    return this.data.bannersData.content[id];
                }.bind(this));
            },
            getBanners: function (bannerId) {
                if (this.data.bannersData.banners.indexOf(bannerId) === -1) {
                    return [];
                }
                var insertedBanner = this.data.bannersData.banners.find(function (element) {
                    return element === bannerId;
                }.bind(this));

                return this.data.bannersData.content[insertedBanner];
            },
            insertBanners: function (firstLoad) {
                var self = this;

                $('[data-role="amasty-banner-container"]').each(function () {
                    var sectionId = $(this).data('position');

                    if (typeof sectionId === "number") {
                        $(this).html(self.getSectionBanners(sectionId).join(''));
                        if (sectionId === 15 && firstLoad) {
                            $('.product-item-inner').append($(self.selectors.BANNER_SELECTOR + sectionId));
                            $(self.selectors.BANNER_SELECTOR + sectionId).show();
                        }
                    } else {
                        var bannerId = $(this).data('bannerid');
                        $(this).html(self.getBanners(bannerId));
                    }

                    self.addProductSidebarClass();
                });
            },

            addProductSidebarClass: function () {
                var sidebarPositions = [1, 2];
                $.each(sidebarPositions, function (index, value) {
                    var positionSelector = '[data-position="' + value + '"]';
                    $(positionSelector).find('li, a.product.photo.product-item-photo, .product.details.product-item-details.product-item-details').addClass('side-banner');
                });
            },

            injectBanners: function () {
                var container = $('[data-role="amasty-banner-container"][data-position='
                    + this.data.bannersData.injectorSectionId + ']');

                if (container.length === 0) {
                    return;
                }

                Object.keys(this.data.bannersData.injectorParams.banners).map(function (id, index) {
                    var params = this.data.bannersData.injectorParams.banners[id];

                    bannerInjector({
                        containerSelector: this.data.bannersData.injectorParams.containerSelector,
                        itemSelector: this.data.bannersData.injectorParams.itemSelector,
                        afterProductRow: params.afterProductRow,
                        afterProductNum: params.afterProductNum,
                        width: params.width}
                    ).inject(container.find('[data-banner-id=' + id + ']')[0]);
                }.bind(this));
            },

            'Amasty_PromoBanners/js/loader': function (settings) {
                var cart = customerData.get('cart');

                $.extend(this.data, settings);
                banner.init();

                cart.subscribe(function (_) {
                    this.getBannersData();
                }.bind(this))
            },

            getBannersData: function () {
                var self = this;
                $.ajax({
                    url: self.data.requestUrl,
                    data: {
                        categoryId: self.data.categoryId,
                        productId: self.data.productId
                    },
                    dataType: 'json',
                    type: 'GET',
                    success: function (data) {
                       $.extend(self.data, data);
                       banner.init(false);
                       $(document).on('amshopby:ajax_filter_applied', banner.init.bind(this));
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log(textStatus);
                    }
                });
            }
        };

    return banner;
});
