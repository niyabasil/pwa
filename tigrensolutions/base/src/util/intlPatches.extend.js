module.exports = targetables => {
    const intlPatches = targetables.esModule(
        `@magento/peregrine/lib/util/intlPatches.js`
    );

    intlPatches.addImport(
        `{ intlFormats } from '@tigrensolutions/base/src/util/intlFormats.js'`
    );

    intlPatches.insertAfterSource('const intlFormats', 'Old');
    intlPatches.insertBeforeSource(
        'toFixed(maximumFractionDigits)',
        'toFixed(format.maximumFractionDigits)',
        { remove: 30 }
    );
    intlPatches
        .insertBeforeSource(
            'return parts.concat([',
            `
        const isHideDecimal = Number(fraction) === 0;

        `
        )
        .insertAfterSource(
            'return parts.concat([',
            `
            {
                type: 'decimal',
                value: isHideDecimal ? undefined : decimal
            },
            {
                type: 'fraction',
                value: isHideDecimal ? undefined : fraction
            },
            {
                type: 'symbolAfter',
                value: format.symbolAfter
            }
        `,
            { remove: 99 }
        );
    intlPatches.insertBeforeSource(
        `this.formatToParts
            ? this.formatToParts(num)
            : IntlPatches.formatToPartsPatch(this.resolvedOptions(), num)`,
        `num
            ? IntlPatches.formatToPartsPatch(this.resolvedOptions(), num)
            : IntlPatches.formatToPartsPatch(this.resolvedOptions(), 0)`,
        { remove: 131 }
    );
};
