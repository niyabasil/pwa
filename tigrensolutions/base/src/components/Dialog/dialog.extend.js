module.exports = (targetables, targetablePath) => {
    const dialog = targetables.reactComponent(targetablePath);
    // Fix scroll lock error after close dialog.
    dialog.addImport('{ useLayoutEffect } from "react"');
    dialog.insertAfterSource(
        'useScrollLock(isOpen);',
        `
        useLayoutEffect(() => {
             return () => {
                document.documentElement.dataset.scrollLock = '';
            }
        }, []);
    `
    );
    dialog.insertBeforeSource(
        '} = props;',
        `,
        disableForm = false,
        `
    );
    dialog.insertBeforeSource(
        'const maybeForm =',
        `const maybeContent =
        (isOpen || !shouldUnmountOnHide) && disableForm ? (
            <div className={classes.form}>
                {/* The Mask. */}
                <button
                    className={classes.mask}
                    disabled={isMaskDisabled}
                    onClick={onCancel}
                    type="reset"
                />
                {/* The Dialog. */}
                <div className={classes.dialog} data-cy={title}>
                    <div className={classes.header}>
                        <span
                            className={classes.headerText}
                            data-cy="Dialog-headerText"
                        >
                            {title}
                        </span>
                        {maybeCloseXButton}
                    </div>
                    <div className={classes.body}>
                        <div className={classes.contents}>{children}</div>
                        {maybeButtons}
                    </div>
                </div>
            </div>
        ) : null;
        `
    );

    dialog.insertBeforeSource('maybeForm}', 'disableForm ? maybeContent : ');
};
