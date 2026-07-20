import React from 'react';
import GiftMessage from '../SuccessPage/giftMessage';
import { mergeClasses } from '@magento/venia-ui/lib/classify';
import defaultClasses from './wrapItem.module.css';

const WrapOrderDetails = component => props => {
    const InnerComponent = component;
    const classes = mergeClasses(defaultClasses, props.classes);

    return (
        <div className={classes.root}>
            <InnerComponent {...props} />
            <GiftMessage
                data={props.item_gift_message}
                classes={{
                    section: classes.section
                }}
                expandable
            />
        </div>
    );
};

export default WrapOrderDetails;
