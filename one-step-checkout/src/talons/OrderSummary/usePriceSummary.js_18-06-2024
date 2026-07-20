import { useRouteMatch } from 'react-router-dom';
import { useQuery } from '@apollo/client';
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from './priceSummary.gql';
import { useMemo } from 'react';

const flattenData = data => {
    if (!data) return {};
    return {
        subtotal: data.cart.prices.subtotal_excluding_tax,
        total: data.cart.prices.grand_total,
        discounts: data.cart.prices.discounts,
        giftCards: data.cart.applied_gift_cards,
        taxes: data.cart.prices.applied_taxes,
        shipping: data.cart.shipping_addresses,
        amGiftWrap: data.cart.amasty_gift_wrap
    };
};

export const usePriceSummary = (props = {}) => {
    const { orderData } = props;
    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const { getPriceSummaryQuery } = operations;

    const [{ cartId }] = useCartContext();
    const match = useRouteMatch('/checkout');
    const isCheckout = !!match;

    const { error, loading, data: queryData } = useQuery(getPriceSummaryQuery, {
        fetchPolicy: 'cache-and-network',
        nextFetchPolicy: 'cache-first',
        skip: !cartId || orderData,
        variables: {
            cartId
        }
    });

    const data = useMemo(() => orderData || queryData, [orderData, queryData]);

    return {
        hasError: !!error,
        hasItems: data && !!data.cart.items.length,
        isCheckout,
        isLoading: !!loading,
        flatData: flattenData(data)
    };
};
