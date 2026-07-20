import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { bool, func, string } from 'prop-types';
import { FormattedMessage } from 'react-intl';

import Button from '@magento/venia-ui/lib/components/Button';
import defaultClasses from './placeOrderButton.module.css';

const PlaceOrderButton = props => {
    const { handlePlaceOrder, isDisabled } = props;

    const classes = useStyle(defaultClasses, props.classes);

    return (
        <div className={classes.root}>
            <Button
                onClick={handlePlaceOrder}
                priority="high"
                classes={{ root_highPriority: classes.button }}
                disabled={isDisabled}
            >
                <FormattedMessage
                    id={'checkoutPage.placeOrder'}
                    defaultMessage={'Place Order'}
                />
            </Button>
        </div>
    );
};

PlaceOrderButton.propTypes = {
    handlePlaceOrder: func,
    className: string,
    isDisabled: bool
};

export default PlaceOrderButton;
