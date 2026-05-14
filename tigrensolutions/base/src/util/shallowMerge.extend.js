module.exports = targetables => {
    //merge tailwind class when compose
    const shallowMerge = targetables.reactComponent(
        '@magento/peregrine/lib/util/shallowMerge.js'
    );

    shallowMerge.addImport(
        `customTwMerge from '@tigrensolutions/base/src/util/tailwind-merge.config.js';`
    );

    shallowMerge.insertAfterSource(
        'const shallowMerge = (...args) =>',
        ` {
    const mergeObject = Object.assign({}, ...args)
    for (const [key, value] of Object.entries(mergeObject)) {
        if(typeof value === 'string') {
            const result = customTwMerge(value.replaceAll(/_/g, ':'));
            mergeObject[key] = result.replaceAll(/:/g, '_');
        }
    }

    return mergeObject
};`,
        { remove: 28 }
    );
};
