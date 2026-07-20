import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';
import defaultClasses from './orderGiftMessage.module.css';
import GiftMessage from '../SuccessPage/giftMessage';

const OrderGiftMessage = props => {
    const { data } = props;
    const classes = useStyle(defaultClasses, props.classes);

    return (
        <GiftMessage
            data={data}
            classes={{
                section: classes.root,
                sectionTitle: classes.heading,
                sectionContent: classes.content
            }}
        />
    );
};

OrderGiftMessage.propTypes = {
    classes: shape({
        root: string
    })
};

export default OrderGiftMessage;
