import React, { useMemo } from 'react';

import { useProductPrice } from '@tigrensolutions/base/src/talons/ProductPrice';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { FormattedMessage } from 'react-intl';
import Price from '@magento/venia-ui/lib/components/Price';
import defaultClasses from './productPrice.module.css';
import RegularPrice from './regularPrice';

const ProductPrice = props => {
    const {
        product,
        optionSelections,
        customizeOptions,
        optionCodes,
        layout,
        showTitle,
        quantity,
        productOptions
    } = props;

    const talonProps = useProductPrice({
        product,
        optionSelections,
        customizeOptions,
        optionCodes,
        quantity,
        productOptions
    });

    const { currency, priceRanges, isShowBoth } = talonProps;

    const classes = useStyle(defaultClasses, props.classes);
    let priceClass;
    switch (layout) {
        case 'listPage':
            priceClass = classes.priceList;
            break;
        case 'productPage':
            priceClass = classes.price;
            break;
        case 'searchPopup':
            priceClass = classes.priceSearch;
            break;
        default:
            priceClass = classes.price;
    }

    const textPriceExclTax = (
        <FormattedMessage
            id="productPrice.textPriceExclTax"
            defaultMessage="Excl. Tax"
        />
    );

    const priceRange = useMemo(() => {
        return priceRanges.map(
            ({ finalValue, regularValue, finalValueExclTax }, index) => {
                const specialClass =
                    finalValue && finalValue < regularValue
                        ? classes.specialPrice
                        : classes.productPrice;
                if (priceRanges.length > 1) {
                    const rowPriceText =
                        index === 0 ? (
                            <FormattedMessage
                                id="productPrice.from"
                                defaultMessage="From"
                            />
                        ) : (
                            <FormattedMessage
                                id="productPrice.to"
                                defaultMessage="To"
                            />
                        );
                    return (
                        <span key={index}>
                            {/* {rowPriceText} */}
                            <span className={specialClass}>
                                <Price
                                    value={finalValue || regularValue}
                                    currencyCode={currency || 'USD'}
                                />
                            </span>
                            {isShowBoth && finalValueExclTax && (
                                <span className={classes.exclPrice}>
                                    {textPriceExclTax}
                                    {': '}
                                    <Price
                                        value={finalValueExclTax}
                                        currencyCode={currency || 'USD'}
                                    />
                                </span>
                            )}
                            <RegularPrice
                                finalValue={finalValue}
                                regularValue={regularValue}
                                classes={classes}
                                showTitle={showTitle}
                                currency={currency}
                            />
                        </span>
                    );
                }

                return (
                    <span key={index}>
                        <span className={specialClass}>
                            <Price
                                value={finalValue || regularValue}
                                currencyCode={currency || 'USD'}
                            />
                        </span>
                        {isShowBoth && finalValueExclTax ? (
                            <span className={classes.exclPrice}>
                                {textPriceExclTax}
                                {': '}
                                <Price
                                    value={finalValueExclTax}
                                    currencyCode={currency || 'USD'}
                                />
                            </span>
                        ) : null}
                        <RegularPrice
                            finalValue={finalValue}
                            regularValue={regularValue}
                            classes={classes}
                            showTitle={showTitle}
                            currency={currency}
                        />
                    </span>
                );
            }
        );
    }, [priceRanges, currency]);

    return (
        <span className={`${classes.root} ${priceClass}`}>{priceRange}</span>
    );
};

export default ProductPrice;
