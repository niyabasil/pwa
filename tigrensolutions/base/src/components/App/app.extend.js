module.exports = (targetables, targetablePath) => {
    // Import Suspense
    const AppComponent = targetables.reactComponent(targetablePath);
    AppComponent.addImport("{ Suspense } from 'react'");
};
