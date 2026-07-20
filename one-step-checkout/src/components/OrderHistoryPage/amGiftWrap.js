import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';
import defaultClasses from './amGiftWrap.module.css';
import GiftWrapSummary from '../OrderSummary/PriceSummary/giftWrapSummary';

const AmGiftWrap = props => {
    const { data } = props;
    const classes = useStyle(defaultClasses, props.classes);

    if (!data || data.amount === null) {
        return null;
    }

    return (
        <div className={classes.root}>
            <GiftWrapSummary {...props} />
        </div>
    );
};

AmGiftWrap.propTypes = {
    classes: shape({
        root: string
    })
};

export default AmGiftWrap;
