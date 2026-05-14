const isModuleAvailable = require('@tigrensolutions/base/helpers/isModuleAvailable');
module.exports = (targetables, targetablePath) => {
    const GalleryItemComponent = targetables.reactComponent(targetablePath);
    const AddToCompareButtonComponent = GalleryItemComponent.addImport(
        `{ AddToCompareButton } from '@tigrensolutions/compare/src/components/AddToCompareButton'`
    );
    GalleryItemComponent.insertBeforeSource(`{wishlistButton}`,`
        <div class={classes.actionsBottons}>`);
    GalleryItemComponent.insertAfterSource(
        `{addButton}`,
        `<${AddToCompareButtonComponent} product={item} classes={classes}/>`
    );
    GalleryItemComponent.insertAfterSource(`<${AddToCompareButtonComponent} product={item} classes={classes}/>`,`
        </div>`);
    if(!isModuleAvailable('@tigrensolutions/core')){
        GalleryItemComponent.addImport(
            'compareClasses from "@tigrensolutions/compare/src/components/Gallery/item.module.css"'
        )
        GalleryItemComponent.insertAfterSource(
            `useStyle(defaultClasses`,
            `, compareClasses`
        );
        GalleryItemComponent.insertAfterSource(`{addButton}`,`
                                        <div class={classes.actionsBottom}>`);
        GalleryItemComponent.insertAfterSource(`{wishlistButton}`,`
                                        </div>`)
    }
};
