module.exports = (targetables, targetablePath) => {
    const headerComponent = targetables.reactComponent(targetablePath);
    headerComponent.addImport(
        `CustomLogo from '@tigrensolutions/base/src/components/Logo/logo.js'`
    );
    headerComponent.replaceJSX(
        `Logo`,
        `CustomLogo classes={{ logo: classes.logo }}`
    );
};
