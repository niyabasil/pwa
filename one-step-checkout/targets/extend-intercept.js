const { Targetables } = require('@magento/pwa-buildpack');

module.exports = targets => {
    const targetables = Targetables.using(targets);

    // OrderDetails
    const OrderDetails = targetables.reactComponent(
        '@magento/venia-ui/lib/components/OrderHistoryPage/OrderDetails/orderDetails.js'
    );
    const AmOrderGiftMessage = OrderDetails.addImport(
        "AmOrderGiftMessage from '@amasty/one-step-checkout/src/components/OrderHistoryPage/orderGiftMessage.js'"
    );
    OrderDetails.setJSXProps('OrderTotal', {
        giftWrapData: '{orderData.amasty_gift_wrap}'
    })
        .appendJSX(
            'div className={classes.itemsContainer}',
            `${AmOrderGiftMessage} data={orderData.order_gift_message}`
        )
        .wrapWithFile(
            '@amasty/one-step-checkout/src/components/OrderHistoryPage/wrapOrderDetails.js'
        );

    // OrderTotal
    const OrderTotal = targetables.reactComponent(
        '@magento/venia-ui/lib/components/OrderHistoryPage/OrderDetails/orderTotal.js'
    );
    const AmGiftWrap = OrderTotal.addImport(
        "AmGiftWrap from '@amasty/one-step-checkout/src/components/OrderHistoryPage/amGiftWrap.js'"
    );
    OrderTotal.insertBeforeJSX(
        'div className={classes.shipping}',
        `${AmGiftWrap} data={props.giftWrapData} currency={grand_total.currency}`
    );

    // Order Item
    const OrderItem = targetables.reactComponent(
        '@magento/venia-ui/lib/components/OrderHistoryPage/OrderDetails/item.js'
    );
    OrderItem.wrapWithFile(
        '@amasty/one-step-checkout/src/components/OrderHistoryPage/wrapItem.js'
    );

    //Billing information
    const BillingInformation = targetables.reactComponent(
        '@magento/venia-ui/lib/components/OrderHistoryPage/OrderDetails/billingInformation.js'
    );
    const AmAddress = BillingInformation.addImport(
        "AmAddress from '@amasty/one-step-checkout/src/components/OrderHistoryPage/amAddress.js'"
    );
    BillingInformation.appendJSX(
        'div className={classes.root}',
        `${AmAddress} data={data}`
    );

    //Shipping information
    const ShippingInformation = targetables.reactComponent(
        '@magento/venia-ui/lib/components/OrderHistoryPage/OrderDetails/shippingInformation.js'
    );
    const AmAddressShip = ShippingInformation.addImport(
        "AmAddressShip from '@amasty/one-step-checkout/src/components/OrderHistoryPage/amAddress.js'"
    );
    ShippingInformation.appendJSX(
        'div className={classes.root}',
        `${AmAddressShip} data={data}`
    );

    // Billing Address
    targetables
        .reactComponent(
            '@magento/venia-ui/lib/components/CheckoutPage/BillingAddress/index.js'
        )
        .insertAfterSource(
            'export { default } from ',
            '"@amasty/one-step-checkout/src/components/BillingAddress"',
            { remove: 25 }
        );

    // CreditCard
    const CreditCard = targetables.reactComponent(
        '@magento/venia-ui/lib/components/CheckoutPage/PaymentInformation/creditCard.js'
    );

    const AmBillingAddress = CreditCard.addImport(
        "AmBillingAddress from '@amasty/one-step-checkout/src/components/BillingAddress'"
    );

    CreditCard.insertAfterJSX(
        'div className={classes.dropin_root}',
        `${AmBillingAddress}
        shouldSubmit={shouldSubmit}
        onBillingAddressChangedError={talonProps.onBillingAddressChangedError}
        onBillingAddressChangedSuccess={talonProps.onBillingAddressChangedSuccess}
        `
    )
        .removeJSX('div className={billingAddressFieldsClassName}')
        .removeJSX('div className={classes.address_check}');
};
