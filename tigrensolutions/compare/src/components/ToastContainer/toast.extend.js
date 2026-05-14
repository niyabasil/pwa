const isModuleAvailable = require('@tigrensolutions/base/helpers/isModuleAvailable');
module.exports = (targetables, targetablePath) => {
    const toast = targetables.reactComponent(targetablePath);
    if(!isModuleAvailable('@tigrensolutions/core')){
        toast.addImport(
            `compareClasses from '@tigrensolutions/compare/src/components/ToastContainer/toast.module.css';`
        );

        toast.insertAfterSource(
            'useStyle(defaultClasses',
            ', compareClasses'
        );
    }
}