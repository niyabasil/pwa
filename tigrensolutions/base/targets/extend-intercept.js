const { Targetables } = require('@magento/pwa-buildpack');
/*
 * We are using the targetable module to add common transforms to React components.
 * With the per project, we should edit the component below.
 * @see https://developer.adobe.com/commerce/pwa-studio/api/buildpack/targetables/TargetableReactComponent
 */
module.exports = targets => {
    const targetables = Targetables.using(targets);

    const richContent = targetables.reactComponent(
        `@magento/venia-ui/lib/components/RichContent/richContent.js`
    );
    richContent.insertBeforeSource(
        `const rendererProps = {`,
        `
    richContentRenderers?.sort((richContentA, richContentB) => {
        if (richContentA.position && richContentB.position) {
            return richContentA.position - richContentB.position;
        } else if (!richContentA.position && richContentB.position) {
            return 1;
        } else if (richContentA.position && !richContentB.position) {
            return -1;
        }

        return 0;
    });
    `
    );
};
