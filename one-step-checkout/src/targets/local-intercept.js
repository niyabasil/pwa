const extendIntercept = require('./extend-intercept');

module.exports = targets => {
    targets.of('@magento/pwa-buildpack').specialFeatures.tap(flags => {
        flags[targets.name] = {
            cssModules: true,
            esModules: true,
            graphqlQueries: true
        };
    });

    const venia = targets.of('@magento/venia-ui');
    const peregrineTargets = targets.of('@magento/peregrine');
    const { routes } = venia;

    routes.tap(routesArray => [
        ...routesArray.filter(route => route.pattern !== '/checkout'),
        {
            name: 'AM Checkout Page',
            pattern: '/checkout',
            exact: true,
            path: targets.name
        }
    ]);

    const talonsTarget = peregrineTargets.talons;
    talonsTarget.tap(({ OrderHistoryPage, CheckoutPage }) => {
        OrderHistoryPage.useOrderHistoryPage.wrapWith(
            targets.name +
                '/src/talons/OrderHistoryPage/wrapUseOrderHistoryPage.js'
        );

        CheckoutPage.OrderConfirmationPage.useCreateAccount.wrapWith(
            targets.name + '/src/talons/SuccessPage/wrapUseCreateAccount.js'
        );

        CheckoutPage.PaymentInformation.useCreditCard.wrapWith(
            targets.name + '/src/talons/PaymentInformation/wrapUseCreditCard.js'
        );
    });

    extendIntercept(targets);
};
