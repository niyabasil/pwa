import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { object, shape, string } from 'prop-types';

import defaultClasses from './amOrderDetails.module.css';
import Delivery from '../SuccessPage/delivery';
import OrderComment from '../SuccessPage/orderComment';

const ConditionalWrapper = props => (props.condition ? props.children : null);

const AmOrderDetails = props => {
    const { orderData } = props;
    const classes = useStyle(defaultClasses, props.classes);
    const { amasty_delivery_date, amasty_order_comment } = orderData;

    const blocksClasses = {
        sectionTitle: classes.heading,
        sectionContent: classes.content
    };

    return (
        <div className={classes.root}>
            <ConditionalWrapper condition={amasty_order_comment}>
                <OrderComment
                    data={amasty_order_comment}
                    classes={blocksClasses}
                />
            </ConditionalWrapper>
            <ConditionalWrapper
                condition={amasty_delivery_date && amasty_delivery_date.date}
            >
                <Delivery data={amasty_delivery_date} classes={blocksClasses} />
            </ConditionalWrapper>
        </div>
    );
};

AmOrderDetails.propTypes = {
    orderData: shape({
        amasty_delivery_date: object,
        amasty_order_comment: string
    }),
    classes: shape({
        root: string
    })
};

export default AmOrderDetails;
