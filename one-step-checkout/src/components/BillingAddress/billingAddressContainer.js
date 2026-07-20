import React from 'react';
import { useAmOscContext } from '../../context';
import BillingAddress from './billingAddress';
import DefaultBillingAddress from '@magento/venia-ui/lib/components/CheckoutPage/BillingAddress/billingAddress';

const BillingAddressContainer = props => {
    const [{ amasty_checkout_general_enabled }] = useAmOscContext();

    if (amasty_checkout_general_enabled) {
        return <BillingAddress {...props} />;
    }

    return <DefaultBillingAddress {...props} />;
};

export default BillingAddressContainer;
