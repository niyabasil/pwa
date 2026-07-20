import React, { Suspense, useMemo } from 'react';
import { fullPageLoadingIndicator } from '@magento/venia-ui/lib/components/LoadingIndicator';
import { useQuery } from '@apollo/client';
import DEFAULT_OPERATIONS from '../../talons/storeConfig.gql';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import AmOscContextProvider from '../../context';

const AmOneStepCheckoutPage = React.lazy(() => import('./oneStepCheckout'));
const DefaultCheckoutPage = React.lazy(() =>
    import('@magento/venia-ui/lib/components/CheckoutPage')
);

const CheckoutPage = (props = {}) => {

    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const { getOscEnabledQuery } = operations;

    const { data } = useQuery(getOscEnabledQuery, {
        fetchPolicy: 'cache-and-network',
        nextFetchPolicy: 'cache-first'
    });

    const [isEnabled, amAllowGuestCheckout] = useMemo(
        () => [
            data && data.storeConfig.amasty_checkout_general_enabled,
            data && data.storeConfig.amasty_checkout_options_guest_checkout
        ],
        [data]
    );

    const CheckoutPageComponent = useMemo(
        () => (isEnabled ? AmOneStepCheckoutPage : DefaultCheckoutPage),
        [isEnabled]
    );

    return (
        <AmOscContextProvider
            amAllowGuestCheckout={amAllowGuestCheckout}
            {...props}
        >
            <Suspense fallback={fullPageLoadingIndicator}>
                <CheckoutPageComponent
                    amAllowGuestCheckout={amAllowGuestCheckout}
                />
            </Suspense>
        </AmOscContextProvider>
    );
};

export default CheckoutPage;
