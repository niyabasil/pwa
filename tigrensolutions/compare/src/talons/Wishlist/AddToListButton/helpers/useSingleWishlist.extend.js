module.exports = (targetables, targetablePath) => {
    // change toast error when add wishlist not login
    const useAddToListButton = targetables.esModule(
        `@magento/peregrine/lib/talons/Wishlist/AddToListButton/helpers/useSingleWishlist.js`
    );
    useAddToListButton.insertBeforeSource(`'info'`, `'error' || `);
};
