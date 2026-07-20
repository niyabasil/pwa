import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';
import { ChevronDown, ChevronUp } from 'react-feather';
import Icon from '@magento/venia-ui/lib/components/Icon';
import { useCheckoutBlock } from '../../talons/CheckoutPage/useCheckoutBlock';
import ScrollAnchor from '@magento/venia-ui/lib/components/ScrollAnchor/scrollAnchor';

const Block = props => {
    const { children, title, block, formKeys } = props;
    const {
        isExpanded,
        handleToggle,
        isHeadingHidden,
        blockRef
    } = useCheckoutBlock({ block, formKeys });
    const classes = useStyle({}, props.classes);

    const toggleIconSrc = isExpanded ? ChevronUp : ChevronDown;
    const toggleIcon = (
        <span className={classes.toggleIcon}>
            <Icon src={toggleIconSrc} size={24} />
        </span>
    );

    const contentClassName = isExpanded
        ? classes.content
        : classes.content_hidden;

    const heading = !isHeadingHidden ? (
        <button
            className={classes.blockHeader}
            type="button"
            onClick={handleToggle}
        >
            {title}
            {toggleIcon}
        </button>
    ) : null;

    return (
        <div className={classes.block}>
            <ScrollAnchor ref={blockRef}>
                {heading}
                <div className={contentClassName}>{children}</div>
            </ScrollAnchor>
        </div>
    );
};

Block.propTypes = {
    title: string,
    block: string,
    classes: shape({
        root: string
    })
};

export default Block;
