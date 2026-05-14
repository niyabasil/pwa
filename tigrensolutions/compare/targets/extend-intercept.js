const { Targetables } = require('@magento/pwa-buildpack');
const isModuleAvailable = require('@tigrensolutions/base/helpers/isModuleAvailable');

/*
 * We are using the targetable module to add common transforms to React components.
 * With the per project, we should edit the component below.
 * @see https://developer.adobe.com/commerce/pwa-studio/api/buildpack/targetables/TargetableReactComponent
 */
module.exports = targets => {
    const targetables = Targetables.using(targets);

    const UserAsyncActions = targetables.reactComponent(
        `@magento/peregrine/lib/store/actions/user/asyncActions`
    );
    UserAsyncActions.insertAfterSource(
        `await dispatch(clearToken());`,
        `storage.removeItem('compareId');`
    );

    if (isModuleAvailable('@amasty/social-login')) {
        const signInEventTalons = targetables.esModule(
            `@amasty/social-login/src/talons/useSignInEvent.js`
        );
        signInEventTalons.addImport(
            "{ useCompareContext } from '@tigrensolutions/compare/src/context'"
        );
        signInEventTalons.insertAfterSource(
            `const fetchCartDetails = useAwaitQuery(getCartDetailsQuery);`,
            `const [, { removeCompare, assignCompare }] = useCompareContext();`
        );
        signInEventTalons.insertAfterSource(
            `getUserDetails({ fetchUserDetails });`,
            `
                    await assignCompare();
                    removeCompare();`
        );
    }
};
