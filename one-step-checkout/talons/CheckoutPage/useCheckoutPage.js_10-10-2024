import { useCallback, useEffect, useMemo, useState } from 'react';
import { useAmOscContext } from '../../context';
import { useUserContext } from '@magento/peregrine/lib/context/user';
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from './osc.gql';
import SHIPPING_OPERATIONS from '@magento/peregrine/lib/talons/CartPage/PriceAdjustments/ShippingMethods/shippingMethods.gql.js';
import ACCOUNT_OPERATIONS from '@magento/peregrine/lib/talons/CreateAccount/createAccount.gql.js';
import { getRegion } from '../../utils';

import {
    useLazyQuery,
    useMutation,
    useApolloClient,
    useQuery
} from '@apollo/client';
import { clearCartDataFromCache } from '@magento/peregrine/lib/Apollo/clearCartDataFromCache';
import CheckoutError from '@magento/peregrine/lib/talons/CheckoutPage/CheckoutError';

export const useCheckoutPage = (props = {}) => {
    const { onSuccessCreateAccount } = props;
    const operations = mergeOperations(
        DEFAULT_OPERATIONS,
        SHIPPING_OPERATIONS,
        ACCOUNT_OPERATIONS,
        props.operations
    );
    const {
        getCheckoutDetailsQuery,
        setShippingAddressMutation,
        getOrderDetailsQuery,
        placeOrderMutation,
        createCartMutation,
        createAccountMutation,
        signInMutation
    } = operations;

    const [
        {
            amasty_checkout_general_title: pageTitle,
            amasty_checkout_general_description: pageDescription,
            amasty_checkout_custom_blocks_top_block_id: topBlockId,
            amasty_checkout_custom_blocks_bottom_block_id: bottomBlockId,
            amasty_checkout_design_bg_color,
            amasty_checkout_design_button_color,
            amasty_checkout_design_heading_color,
            amasty_checkout_design_summary_color,
            loading: configLoading,
            defaultShippingAddress,
            amasty_checkout_additional_options_create_account: createAccountOption,
            amasty_checkout_additional_configuration,
            amasty_checkout_additional_options_automatically_login: signInAfterCreateAccount,
            shouldSubmit,
            isUpdating,
            errors,
            isDoneMap,
            shouldPaymentSubmit,
            isPaymentDone,
            password,
            isDisabledCheckout
        },
        {
            setPlaceOrderButtonClicked,
            setShouldPaymentSubmit,
            setIsPaymentDone,
            setIsUpdating
        }
    ] = useAmOscContext();

    const apolloClient = useApolloClient();
    const [activeContent, setActiveContent] = useState('checkout');
    const [isPlacingOrder, setIsPlacingOrder] = useState(false);

    const [{ isSignedIn }, { setToken }] = useUserContext();
    const [{ cartId }, { createCart, removeCart }] = useCartContext();

    // Queries
    const {
        data: checkoutData,
        networkStatus: checkoutQueryNetworkStatus
    } = useQuery(getCheckoutDetailsQuery, {
        /**
         * Skip fetching checkout details if the `cartId`
         * is a falsy value.
         */
        skip: !cartId || isDisabledCheckout,
        notifyOnNetworkStatusChange: true,
        fetchPolicy: 'cache-and-network',
        nextFetchPolicy: 'cache-first',
        variables: {
            cartId
        }
    });

    const [fetchCartId] = useMutation(createCartMutation);
    const [
        placeOrder,
        { data: placeOrderData, error: placeOrderError }
    ] = useMutation(placeOrderMutation);

    const [
        setShippingAddress,
        { loading: setShippingAddressLoading }
    ] = useMutation(setShippingAddressMutation);

    const [getOrderDetails, { data: orderDetailsData }] = useLazyQuery(
        getOrderDetailsQuery,
        {
            fetchPolicy: 'no-cache'
        }
    );

    const [createAccount] = useMutation(createAccountMutation, {
        fetchPolicy: 'no-cache'
    });

    const [signIn] = useMutation(signInMutation, {
        fetchPolicy: 'no-cache'
    });

    //

    const cartItems = useMemo(() => {
        return (checkoutData && checkoutData.cart.items) || [];
    }, [checkoutData]);

    const isCartEmpty = !cartItems.length;

    const isReadyForPayment = useMemo(() => {
        const hasErrors = errors.size;
        const isAllSectionDone =
            isDoneMap.size && Array.from(isDoneMap.values()).every(e => e);

        return shouldSubmit && !hasErrors && isAllSectionDone;
    }, [errors, isDoneMap, shouldSubmit]);

    const handlePlaceOrder = useCallback(() => {
        setPlaceOrderButtonClicked(true);
    }, [setPlaceOrderButtonClicked]);

    const fetchOrderDetails = useCallback(async () => {
        await getOrderDetails({
            variables: {
                cartId
            }
        });
        setIsPaymentDone(false);
    }, [getOrderDetails, cartId, setIsPaymentDone]);

    const runPlaceOrderWorkFlow = useCallback(async () => {
        try {
            if (cartId) {
                const variables = { cartId };
                await placeOrder({ variables });

                if (createAccountOption === 2 && password) {
                    // create account while place order
                    const { email, shipping_addresses } =
                        checkoutData.cart || {};
                    const { firstname, lastname } = shipping_addresses[0];

                    await createAccount({
                        variables: {
                            email,
                            firstname,
                            lastname,
                            password,
                            is_subscribed: false
                        }
                    });

                    if (onSuccessCreateAccount) {
                        onSuccessCreateAccount();
                    }

                    if (signInAfterCreateAccount) {
                        const signInResponse = await signIn({
                            variables: {
                                email,
                                password
                            }
                        });
                        const token =
                            signInResponse.data.generateCustomerToken.token;
                        await setToken(token);
                    }
                }

                // Cleanup stale cart and customer info.
                await removeCart();
                await clearCartDataFromCache(apolloClient);

                await createCart({
                    fetchCartId
                });
            }
        } catch (err) {
            console.error(
                'An error occurred during when placing the order',
                err
            );
            setIsPlacingOrder(false);
        }
        setIsPlacingOrder(false);
    }, [
        cartId,
        placeOrder,
        apolloClient,
        removeCart,
        fetchCartId,
        createCart,
        password,
        createAccount,
        signInAfterCreateAccount,
        setToken,
        checkoutData,
        createAccountOption,
        signIn,
        onSuccessCreateAccount
    ]);

    useEffect(() => {
        if (isReadyForPayment) {
            setShouldPaymentSubmit();
        }
    }, [isReadyForPayment, setShouldPaymentSubmit]);

    useEffect(() => {
        if (isPaymentDone) {
            setIsPlacingOrder(true);
            fetchOrderDetails();
        }
    }, [isPaymentDone, fetchOrderDetails]);

    useEffect(() => {
        if (orderDetailsData && isPlacingOrder) {
            runPlaceOrderWorkFlow();
        }
    }, [orderDetailsData, isPlacingOrder, runPlaceOrderWorkFlow]);

    // set default estimate shippingMethods
    useEffect(() => {
        const { cart } = checkoutData || {};

        const setShippingAddressForEstimate = async () => {
            await setShippingAddress({
                variables: {
                    cartId,
                    address: {
                        ...defaultShippingAddress,
                        region: getRegion(defaultShippingAddress.region)
                    }
                }
            });
        };

        if (
            !isCartEmpty &&
            !(cart.shipping_addresses && cart.shipping_addresses.length) &&
            defaultShippingAddress
        ) {
            setShippingAddressForEstimate();
        }
    }, [
        cartId,
        setShippingAddress,
        checkoutData,
        defaultShippingAddress,
        isCartEmpty
    ]);

    // Go back to checkout if shopper logs in
    useEffect(() => {
        if (isSignedIn) {
            setActiveContent('checkout');
        }
    }, [isSignedIn]);

    useEffect(() => {
        globalThis.scrollTo({
            left: 0,
            top: 0,
            behavior: 'smooth'
        });
    }, [activeContent]);

    const toggleSignInContent = useCallback(() => {
        setActiveContent(currentlyActive =>
            currentlyActive === 'checkout' ? 'signIn' : 'checkout'
        );
    }, [setActiveContent]);

    const isLoading = useMemo(() => {
        const checkoutQueryInFlight = checkoutQueryNetworkStatus
            ? checkoutQueryNetworkStatus < 7
            : true;

        return (
            checkoutQueryInFlight || configLoading || setShippingAddressLoading
        );
    }, [checkoutQueryNetworkStatus, configLoading, setShippingAddressLoading]);

    const colorScheme = useMemo(
        () => ({
            '--am-osc-content-background': amasty_checkout_design_bg_color,
            '--am-osc-heading-color': amasty_checkout_design_heading_color,
            '--am-osc-summary-background': amasty_checkout_design_summary_color,
            '--am-osc-order-btn-background': amasty_checkout_design_button_color,
            '--am-osc-font':
                amasty_checkout_additional_configuration &&
                amasty_checkout_additional_configuration.amasty_checkout_design_font
        }),
        [
            amasty_checkout_design_bg_color,
            amasty_checkout_design_button_color,
            amasty_checkout_design_heading_color,
            amasty_checkout_design_summary_color,
            amasty_checkout_additional_configuration
        ]
    );

    const checkoutError = useMemo(() => {
        if (placeOrderError) {
            return new CheckoutError(placeOrderError);
        }
    }, [placeOrderError]);

    return {
        pageTitle,
        pageDescription,
        isLoading,
        activeContent,
        toggleSignInContent,
        isGuestCheckout: !isSignedIn,
        checkoutInformationData: checkoutData ? checkoutData.cart : {},
        availablePaymentMethods: checkoutData
            ? checkoutData.cart.available_payment_methods
            : null,
        handlePlaceOrder,
        cartItems,
        isCartEmpty,
        isUpdating,
        topBlockId,
        bottomBlockId,
        setIsUpdating,
        orderDetailsData,
        order: (placeOrderData && placeOrderData.placeOrder.order) || null,
        isPlacingOrder: isPlacingOrder || isReadyForPayment,
        isPlaceOrderButtonDisabled:
            isUpdating || isPlacingOrder || shouldSubmit || shouldPaymentSubmit,
        colorScheme,
        isDisabledCheckout,
        hasError: !!checkoutError,
        error: checkoutError
    };
};
