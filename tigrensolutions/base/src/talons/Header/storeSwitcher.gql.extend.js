module.exports = (targetables, targetablePath) => {
    // add query tax_display_type
    const storeSwitcherGraphql = targetables.esModule(targetablePath);
    storeSwitcherGraphql.insertAfterSource(
        `storeConfig {`,
        `
            tax_display_type`
    );
};
