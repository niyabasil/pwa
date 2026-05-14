module.exports = (targetables, targetablePath) => {
    const ProductFullDetailComponent = targetables.reactComponent(
        targetablePath
    );
    const AddToCompareButtonProductFullDetail = ProductFullDetailComponent.addImport(
        `{ AddToCompareButton } from '@tigrensolutions/compare/src/components/AddToCompareButton'`
    );
    ProductFullDetailComponent.insertAfterSource(
         `<WishlistButton {...wishlistButtonProps} />`,
        `<${AddToCompareButtonProductFullDetail} product={product} classes={classes} isProductDetail={true}/>`
    );
};
