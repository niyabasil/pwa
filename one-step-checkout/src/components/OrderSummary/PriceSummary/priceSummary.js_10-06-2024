import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { bool, shape, string } from 'prop-types';
import defaultClasses from '@magento/venia-ui/lib/components/CartPage/PriceSummary/priceSummary.module.css';
import additionalClasses from './priceSummary.module.css';
import { usePriceSummary } from '../../../talons/OrderSummary/usePriceSummary';
import { FormattedMessage, useIntl } from 'react-intl';
import Price from '@magento/venia-ui/lib/components/Price';
import DiscountSummary from '@magento/venia-ui/lib/components/CartPage/PriceSummary/discountSummary';
import GiftCardSummary from '@magento/venia-ui/lib/components/CartPage/PriceSummary/giftCardSummary.ce';
import TaxSummary from '@magento/venia-ui/lib/components/CartPage/PriceSummary/taxSummary';
import ShippingSummary from '@magento/venia-ui/lib/components/CartPage/PriceSummary/shippingSummary';
import GiftWrapSummary from './giftWrapSummary';

const PriceSummary = props => {
    const { isUpdating, orderData } = props;
    const classes = useStyle(defaultClasses, additionalClasses, props.classes);
    const talonProps = usePriceSummary({ orderData });

    const { hasError, hasItems, isCheckout, isLoading, flatData } = talonProps;

    const { formatMessage } = useIntl();

    if (hasError) {
        return (
            <div className={classes.root}>
                <span className={classes.errorText}>
                    <FormattedMessage
                        id={'priceSummary.errorText'}
                        defaultMessage={
                            'Something went wrong. Please refresh and try again.'
                        }
                    />
                </span>
            </div>
        );
    } else if (!hasItems) {
        return null;
    }

    const {
        subtotal,
        total,
        discounts,
        giftCards,
        taxes,
        shipping,
        amGiftWrap
    } = flatData;

    const isPriceUpdating = isUpdating || isLoading;
    const priceClass = isPriceUpdating ? classes.priceUpdating : classes.price;
    const totalPriceClass = isPriceUpdating
        ? classes.priceUpdating
        : classes.totalPrice;

    const totalPriceLabel = formatMessage({
        id: 'priceSummary.total',
        defaultMessage: 'Total'
    });

    return (
        <div className={classes.root}>
            <div className={classes.lineItems}>
                <span className={classes.lineItemLabel}>
                    <FormattedMessage
                        id={'priceSummary.lineItemLabel'}
                        defaultMessage={'Subtotal'}
                    />
                </span>
                <span className={priceClass}>
                    <Price
                        value={subtotal.value}
                        currencyCode={subtotal.currency}
                    />
                </span>
                <DiscountSummary
                    classes={{
                        lineItemLabel: classes.lineItemLabel,
                        price: priceClass
                    }}
                    data={discounts}
                />
                <GiftCardSummary
                    classes={{
                        lineItemLabel: classes.lineItemLabel,
                        price: priceClass
                    }}
                    data={giftCards}
                />
                <GiftWrapSummary
                    classes={{
                        lineItemLabel: classes.lineItemLabel,
                        price: priceClass
                    }}
                    data={amGiftWrap}
                    currency={total.currency}
                />
                <TaxSummary
                    classes={{
                        lineItemLabel: classes.lineItemLabel,
                        price: priceClass
                    }}
                    data={taxes}
                    isCheckout={isCheckout}
                />
                <ShippingSummary
                    classes={{
                        lineItemLabel: classes.lineItemLabel,
                        price: priceClass
                    }}
                    data={shipping}
                    isCheckout={isCheckout}
                />
                <span className={classes.totalLabel}>{totalPriceLabel}</span>
                <span className={totalPriceClass}>
                    <Price value={total.value} currencyCode={total.currency} />
                </span>
            </div>
        </div>
    );
};

PriceSummary.propTypes = {
    isUpdating: bool,
    classes: shape({
        root: string
    })
};

export default PriceSummary;
