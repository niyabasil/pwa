module.exports = (targetables, targetablePath) => {
    // Add compare button
    const NavigationComponent = targetables.reactComponent(targetablePath);
    const ComparePopupComponent = NavigationComponent.addReactLazyImport(
        `@tigrensolutions/compare/src/components/ComparePopup`
    );
    NavigationComponent.insertBeforeSource(
        `<aside className={rootClassName}`,
        `<>
        <Suspense fallback={null}><${ComparePopupComponent} /></Suspense>
        `
    ).insertAfterSource(
        `</aside>`,
        `
    </>`
    );
};
