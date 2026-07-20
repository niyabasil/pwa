import React, { useMemo } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string, bool } from 'prop-types';
import defaultClasses from './checkoutContent.module.css';
import ShippingInformation from '../ShippingInformation';
import ShippingMethod from '../ShippingMethod';
import PaymentInformation from '../PaymentInformation';
import Delivery from '../Delivery';
import OrderSummary from '../OrderSummary';
import { useCheckoutContent } from '../../talons/CheckoutPage/useCheckoutContent';
import Block from './block';

const sectionsMap = {
    shipping_address: {
        component: ShippingInformation,
        formKeys: ['ADDRESS_FORM', 'GUEST_FIELDS']
    },
    shipping_method: {
        component: ShippingMethod,
        formKeys: ['SHIPPING_METHOD']
    },
    payment_method: {
        component: PaymentInformation,
        formKeys: ['PAYMENT']
    },
    delivery: {
        component: Delivery,
        isEnabledOption: 'amasty_checkout_delivery_date_enabled',
        formKeys: ['DELIVERY']
    },
    summary: {
        component: OrderSummary,
        formKeys: ['ADDITIONAL_FIELDS', 'AGREEMENT']
    }
};

const CheckoutContent = props => {
    const { isPlacingOrder } = props;
    const { layout, classNames, design, isBlockEnabled } = useCheckoutContent();
    const classes = useStyle(defaultClasses, props.classes);

    const columns = useMemo(() => {
        return layout.map((column, idx) => (
            <div className={classes.col} key={idx}>
                {column.map(({ name, title }) => {
                    const section = sectionsMap[name];

                    if (!section || !isBlockEnabled(section)) {
                        return null;
                    }

                    const Component = section.component;

                    return (
                        <Block
                            classes={classes}
                            key={name}
                            block={name}
                            title={title}
                            formKeys={section.formKeys}
                        >
                            <Component design={design} {...props} />
                        </Block>
                    );
                })}
            </div>
        ));
    }, [props, layout, design, classes, isBlockEnabled]);

    const rootClass = [
        !isPlacingOrder ? classes.root : classes.root_disabled,
        ...classNames.map(c => classes[c])
    ].join(' ');

    return <div className={rootClass}>{columns}</div>;
};

CheckoutContent.propTypes = {
    isPlacingOrder: bool,
    classes: shape({
        root: string
    })
};

export default CheckoutContent;
