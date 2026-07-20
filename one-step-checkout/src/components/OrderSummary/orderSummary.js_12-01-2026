import React from 'react';
import { bool, func, shape, string } from 'prop-types';
import { useIntl } from 'react-intl';
import PriceSummary from './PriceSummary';
import { useStyle } from '@magento/venia-ui/lib/classify';
import ProductListing from './ProductListing';
import AdditionalOptions from '../AdditionalOptions';
import defaultClasses from './orderSummary.css';
import { useAmOscContext } from '../../context';
import PlaceOrderButton from './placeOrderButton';

const OrderSummary = props => {
    const {
        isUpdating,
        setIsUpdating,
        blockTitle,
        checkoutInformationData,
        handlePlaceOrder,
        design,
        isPlaceOrderButtonDisabled
    } = props;

    const { formatMessage } = useIntl();
    const [
        { amasty_checkout_design_place_button_layout: buttonPosition }
    ] = useAmOscContext();
    const classes = useStyle(defaultClasses, props.classes);

    const title =
        blockTitle ||
        formatMessage({
            id: 'amOsc.orderSummaryTitle',
            defaultMessage: 'Order Summary'
        });

    const placeOrderButton =
        buttonPosition === 'summary' ? (
            <PlaceOrderButton
                isDisabled={isPlaceOrderButtonDisabled}
                handlePlaceOrder={handlePlaceOrder}
            />
        ) : null;

    const rootClasses = [classes.root, classes[design]].join(' ');

    return (
        <div className={rootClasses}>
            <h3 className={classes.heading}>{title}</h3>

            <div className={classes.cartContent}>
                <ProductListing setIsCartUpdating={setIsUpdating} />
                <PriceSummary isUpdating={isUpdating} />
            </div>
            <div className={classes.options}>
                <AdditionalOptions
                    setIsUpdating={setIsUpdating}
                    checkoutInformationData={checkoutInformationData}
                />

                {placeOrderButton}
            </div>
        </div>
    );
};

OrderSummary.propTypes = {
    setIsUpdating: func,
    isUpdating: bool,
    classes: shape({
        root: string,
        title: string
    })
};

export default OrderSummary;
