module.exports = (targetables, targetablePath) => {
    const itemGallery = targetables.reactComponent(targetablePath);

    itemGallery.setJSXProps(`Price`, {
        type: `{'full'}`,
        product: '{item}',
        layout: `{'listPage'}`
    });
};
