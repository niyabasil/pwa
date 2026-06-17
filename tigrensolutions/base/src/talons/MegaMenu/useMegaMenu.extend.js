/*
 * Sort MegaMenu subcategories alphabetically at the frontend level.
 * This complements the backend Children resolver plugin.
 */
module.exports = (targetables, targetablePath) => {
    const useMegaMenu = targetables.esModule(targetablePath);

    useMegaMenu.insertAfterSource(
        `const buildCategoryTree = (rootCategory, branch) => {`,
        `
    // Sort children alphabetically before building the tree
    if (branch && branch.children) {
        branch.children = [...branch.children].sort((a, b) =>
            (a.name || '').localeCompare(b.name || '')
        );
    }
`
    );
};
