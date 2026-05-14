module.exports = (targetables, targetablePath) => {
    const addToCartGQL = targetables.esModule(targetablePath);
    addToCartGQL.insertAfterSource(
        `...MiniCartFragment
            }`,
        `user_errors{message code}`
    );
};
