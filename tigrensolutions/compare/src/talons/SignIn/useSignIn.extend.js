module.exports = (targetables, targetablePath) => {
    const SignInTalons = targetables.reactComponent(targetablePath);
    SignInTalons.addImport(
        `{ useCompareContext } from '@tigrensolutions/compare/src/context'`
    );
    SignInTalons.insertAfterSource(
        `const fetchCartDetails = useAwaitQuery(getCartDetailsQuery);`,
        `const [, { removeCompare, assignCompare }] = useCompareContext();`
    );
    SignInTalons.insertAfterSource(
        `getCartDetails({ fetchCartId, fetchCartDetails });`,
        `await assignCompare();
        removeCompare();`
    );
};
