const isModuleAvailable = require('@tigrensolutions/base/helpers/isModuleAvailable');

module.exports = (targetables, targetablePath) => {
    const WishlistItemComponent = targetables.reactComponent(targetablePath);
    const AddToCompareButtonWishlistItem = WishlistItemComponent.addImport(
        `{ AddToCompareButton } from '@tigrensolutions/compare/src/components/AddToCompareButton'`
    );

    if (!isModuleAvailable(`@tigrensolutions/core`)) {
        WishlistItemComponent.insertAfterSource(
            `{addToCart}`,
            `<${AddToCompareButtonWishlistItem} product={product} isWishlistPage={true} />`
        );
    } else {
        WishlistItemComponent.insertAfterSource(
            `<div className={classes.actionsBottom}>`,
            `<${AddToCompareButtonWishlistItem} product={product} isWishlistPage={true} />`
        );
    }
};
