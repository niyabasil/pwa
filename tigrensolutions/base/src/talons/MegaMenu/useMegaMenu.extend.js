/*
 * Sort MegaMenu subcategories alphabetically by name.
 * useMegaMenu.js sorts children by position — we replace that with a name sort.
 */
module.exports = (targetables, targetablePath) => {
    const useMegaMenu = targetables.esModule(targetablePath);

    useMegaMenu.replaceSource(
        `.sort((a, b) => (a.position > b.position ? 1 : -1))`,
        `.sort((a, b) => (a.name || '').localeCompare(b.name || ''))`
    );
};
