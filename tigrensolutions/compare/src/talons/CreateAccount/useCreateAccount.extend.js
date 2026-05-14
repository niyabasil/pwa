module.exports = (targetables, targetablePath) => {
    const CreateTalons = targetables.reactComponent(targetablePath);
    CreateTalons.addImport(
        `{ useCompareContext } from '@tigrensolutions/compare/src/context'`
    );
    CreateTalons.insertAfterSource(
        `const fetchCartDetails = useAwaitQuery(getCartDetailsQuery);`,
        `const [, { removeCompare, assignCompare }] = useCompareContext();`
    );
    CreateTalons.insertAfterSource(
        `await getUserDetails({ fetchUserDetails });`,
        `await assignCompare();
        removeCompare();`
    );
};
