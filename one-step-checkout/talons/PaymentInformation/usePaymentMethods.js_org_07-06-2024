import { useAmOscContext } from '../../context';
import { useMemo } from 'react';
import payments from '@magento/venia-ui/lib/components/CheckoutPage/PaymentInformation/paymentMethodCollection';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from '@magento/peregrine/lib/talons/CheckoutPage/PaymentInformation/paymentMethods.gql';
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import { useQuery } from '@apollo/client';
import useFieldState from '@magento/peregrine/lib/hooks/hook-wrappers/useInformedFieldStateWrapper';

export const usePaymentMethods = (props = {}) => {
    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const { getPaymentMethodsQuery } = operations;

    const [{ cartId }] = useCartContext();

    const { data, loading } = useQuery(getPaymentMethodsQuery, {
        skip: !cartId,
        variables: { cartId }
    });

    const { value: currentSelectedPaymentMethod } = useFieldState(
        'selectedPaymentMethod'
    );

    const availablePaymentMethods =
        (data && data.cart.available_payment_methods) || [];

    const [
        {
            amasty_checkout_default_values_payment_method: defaultPaymentMethod,
            amasty_checkout_design_place_button_layout: placeOrderButtonPosition,
            amasty_checkout_additional_options_display_agreements: agreementsPosition
        }
    ] = useAmOscContext();

    const initialSelectedMethod = useMemo(() => {
        const paymentMethods = Object.keys(payments);

        if (
            paymentMethods.length === 1 ||
            !defaultPaymentMethod ||
            !paymentMethods.includes(defaultPaymentMethod)
        ) {
            return paymentMethods[0];
        }

        return defaultPaymentMethod;
    }, [defaultPaymentMethod]);

    return {
        availablePaymentMethods,
        currentSelectedPaymentMethod,
        initialSelectedMethod,
        isLoading: loading,
        isShowPlaceOrderButton: placeOrderButtonPosition === 'payment',
        isShowAgreements: agreementsPosition === 'payment_method'
    };
};
