import React, { Fragment } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';
import { FormattedMessage } from 'react-intl';
import Price from '@magento/venia-ui/lib/components/Price';

const GiftWrapSummary = props => {
    const { data, currency } = props;
    const classes = useStyle({}, props.classes);

    if (!data || data.amount === null) {
        return null;
    }

    return (
        <Fragment>
            <span className={classes.lineItemLabel}>
                <FormattedMessage
                    id={'amOsc.giftWrapLabel'}
                    defaultMessage={'Gift wrap'}
                />
            </span>
            <span className={classes.price}>
                <Price
                    value={Number(data.amount)}
                    currencyCode={data.currency_code || currency}
                />
            </span>
        </Fragment>
    );
};

GiftWrapSummary.propTypes = {
    classes: shape({
        root: string
    })
};

GiftWrapSummary.defaultProps = {
    currency: 'USD'
};

export default GiftWrapSummary;
