import React, { Fragment } from 'react';
import { FormattedMessage } from 'react-intl';
import { number, string, shape, array } from 'prop-types';
import Price from '@magento/venia-ui/lib/components/Price';

import { useStyle } from '@magento/venia-ui/lib/classify';
import defaultClasses from './shippingRadio.module.css';

const ShippingRadio = props => {
    const { price, name, title, currency, shippingMethods  } = props;
    const priceElement = props.price ? (
        <Price value={price} currencyCode={currency} />
    ) : (

        <span>
            <FormattedMessage id={'global.free'} defaultMessage={'FREE'} />
        </span>
    );

    const classes = useStyle(defaultClasses, props.classes);
    return (
        <Fragment>
            <div className={classes.price}>{priceElement}</div>
            <span>{name}</span>
            <span>{title}</span>
        </Fragment>
    );
};

export default ShippingRadio;

ShippingRadio.propTypes = {
    classes: shape({
        price: string
    }),
    currency: string.isRequired,
    name: string,
    price: number.isRequired,
    title: string,
    shippingMethods: array 
};
