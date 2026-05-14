module.exports = (targetables, targetablePath) => {
    const ContextProvider = targetables.reactComponent(targetablePath);
    const CompareProvider = ContextProvider.addImport(
        `CompareProvider from '@tigrensolutions/compare/src/context'`
    );
    ContextProvider.insertBeforeSource(
        `const ContextProvider = ({ children }) => {`,
        `contextProviders.push(${CompareProvider});`
    );
};
