module.exports = (targetables, targetablePath) => {
    const wishlistItemFragment = targetables.reactComponent(targetablePath);
    wishlistItemFragment.insertAfterSource(
        `product {`,
        `
            id
            `
    );
};
