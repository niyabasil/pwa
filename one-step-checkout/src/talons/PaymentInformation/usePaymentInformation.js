import { useCallback, useEffect, useMemo, useState } from 'react';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from '@magento/peregrine/lib/talons/CheckoutPage/PaymentInformation/paymentInformation.gql';
import { useApolloClient, useMutation } from '@apollo/client';
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import CheckoutError from '@magento/peregrine/lib/talons/CheckoutPage/CheckoutError';
import { useAmOscContext } from '../../context';

const FORM_KEY = 'PAYMENT';

export const usePaymentInformation = (props = {}) => {
    const { checkoutInformationData: paymentInformationData } = props;
    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);

    const {
        getPaymentNonceQuery,
        setBillingAddressMutation,
        setFreePaymentMethodMutation
    } = operations;

    const [{ cartId }] = useCartContext();
    const [
        { shouldPaymentSubmit },
        { setIsPaymentDone, setSectionError, checkoutError, resetShouldSubmit }
    ] = useAmOscContext();

    const client = useApolloClient();
    const [doneEditing, setDoneEditing] = useState(false);

    const handlePaymentSuccess = useCallback(() => setIsPaymentDone(true), [
        setIsPaymentDone
    ]);
    const handlePaymentError = useCallback(
        error => setSectionError([FORM_KEY, error]),
        [setSectionError]
    );

    const [setFreePaymentMethod] = useMutation(setFreePaymentMethodMutation, {
        onCompleted: handlePaymentSuccess
    });

    const clearPaymentDetails = useCallback(() => {
        client.writeQuery({
            query: getPaymentNonceQuery,
            data: {
                cart: {
                    __typename: 'Cart',
                    id: cartId,
                    paymentNonce: null
                }
            }
        });
    }, [cartId, client, getPaymentNonceQuery]);

    const handleExpiredPaymentError = useCallback(() => {
        clearPaymentDetails({ variables: { cartId } });
        setIsPaymentDone(false);
    }, [clearPaymentDetails, cartId, setIsPaymentDone]);

    const [setBillingAddress] = useMutation(setBillingAddressMutation);

    /**
     * Effects
     */

    const [
        availablePaymentMethods,
        selectedPaymentMethod,
        shippingAddressOnCart,
        selectedPaymentMethodTitle
    ] = useMemo(() => {
        const {
            available_payment_methods,
            selected_payment_method,
            shipping_addresses
        } = paymentInformationData || {};

        return [
            available_payment_methods || [],
            selected_payment_method ? selected_payment_method.code : null,
            shipping_addresses.length ? shipping_addresses[0] : null,
            selected_payment_method ? selected_payment_method.title : null
        ];
    }, [paymentInformationData]);

    // Whenever selected payment method is no longer an available method we
    // should reset to the payment step to force the user to select again.
    useEffect(() => {
        if (
            !availablePaymentMethods.find(
                ({ code }) => code === selectedPaymentMethod
            )
        ) {
            setIsPaymentDone(false);
            setDoneEditing(false);
        }
    }, [availablePaymentMethods, selectedPaymentMethod, setIsPaymentDone]);

    useEffect(() => {
        if (
            checkoutError instanceof CheckoutError &&
            checkoutError.hasPaymentExpired()
        ) {
            handleExpiredPaymentError();
        }
    }, [checkoutError, handleExpiredPaymentError]);

    // If free is ever available and not selected, automatically select it.
    useEffect(() => {
        const setFreeIfAvailable = async () => {
            const freeIsAvailable = !!availablePaymentMethods.find(
                ({ code }) => code === 'free'
            );
            if (freeIsAvailable) {
                if (selectedPaymentMethod !== 'free') {
                    await setFreePaymentMethod({
                        variables: {
                            cartId
                        }
                    });
                    setDoneEditing(true);
                } else {
                    setDoneEditing(true);
                }
            }
        };
        setFreeIfAvailable();
    }, [
        availablePaymentMethods,
        cartId,
        selectedPaymentMethod,
        setFreePaymentMethod,
        setDoneEditing
    ]);

    // If the selected payment method is "free" keep the shipping address
    // synced with billing address.This _requires_ the UI does not allow payment
    // information before shipping address.
    useEffect(() => {
        if (selectedPaymentMethod === 'free' && shippingAddressOnCart) {
            const {
                firstname,
                lastname,
                street,
                city,
                region,
                postcode,
                country,
                telephone
            } = shippingAddressOnCart;
            const regionCode = region.code;
            const countryCode = country.code;

            setBillingAddress({
                variables: {
                    cartId,
                    firstname,
                    lastname,
                    street,
                    city,
                    regionCode,
                    postcode,
                    countryCode,
                    telephone
                }
            });
        }
    }, [
        cartId,
        selectedPaymentMethod,
        setBillingAddress,
        shippingAddressOnCart
    ]);

    return {
        shouldPaymentSubmit,
        handlePaymentSuccess,
        handlePaymentError,
        resetShouldSubmit,
        doneEditing,
        selectedPaymentMethodTitle
    };
};
