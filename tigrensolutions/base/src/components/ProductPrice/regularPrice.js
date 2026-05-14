import React from 'react';

import { useStyle } from '@magento/venia-ui/lib/classify';
import { FormattedMessage } from 'react-intl';
import Price from '@magento/venia-ui/lib/components/Price';

const RegularPrice = props => {
    const { finalValue, regularValue, currency, showTitle } = props;
    const classes = useStyle(props.classes);

    return (
        <>
            {finalValue && finalValue < regularValue ? (
                <>
                    {showTitle && (
                        <span className={classes.regularTitle}>
                            <FormattedMessage
                                id="productPrice.normalPrice"
                                defaultMessage="Normal Price"
                            />
                        </span>
                    )}
                    <span className={classes.oldPrice}>
                        <Price
                            value={regularValue}
                            currencyCode={currency || 'USD'}
                        />
                    </span>
                </>
            ) : null}
        </>
    );
};

export default RegularPrice;
