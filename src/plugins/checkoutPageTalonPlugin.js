/**
 * Copyright © 2021 MultiSafepay, Inc. All rights reserved.
 * See DISCLAIMER.md for disclaimer details.
 *
 * @license OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * @package @multisafepay/multisafepay-payment-integration
 * @link https://github.com/MultiSafepay/pwastudio-multisafepay-payment-integration
 *
 */
import React, {useCallback, useEffect, useState} from 'react';
import {AlertCircle as AlertCircleIcon} from 'react-feather';

import {useCartContext} from '@magento/peregrine/lib/context/cart';
import EWAY_OPERATIONS from '../talons/checkoutPageOrderPlace.gql.js';
import EWAY_URL_OPERATIONS from '../talons/ewayurl.gql.js';
import DEFAULT_OPERATIONS from '@magento/peregrine/lib/talons/CheckoutPage/checkoutPage.gql.js';
import {useToasts} from '@magento/peregrine';

import veniaPjson from '@magento/venia-ui/package.json';
import peregrinePjson from '@magento/peregrine/package.json';

import {
    useApolloClient,
    useLazyQuery,
    useMutation
} from '@apollo/client';

import {clearCartDataFromCache} from "@magento/peregrine/lib/Apollo/clearCartDataFromCache";
import {CHECKOUT_STEP} from "@magento/peregrine/lib/talons/CheckoutPage/useCheckoutPage";
import mergeOperations from "@magento/peregrine/lib/util/shallowMerge";
import Icon from "@magento/venia-ui/lib/components/Icon";

const wrapUseCheckoutPage = (original) => {
    return function useCheckoutPage(...args) {
        const operations = mergeOperations(DEFAULT_OPERATIONS, EWAY_OPERATIONS);
        const result = original(...args);
        const errorIcon = <Icon src={AlertCircleIcon} size={20}/>;

        const {
            isLoading,
            setCheckoutStep,
            resetReviewOrderButtonClicked
        } = result;

        const {
            createCartMutation,
            getOrderDetailsQuery,
            restoreQuoteMutation,
            placeEwayOrderMutation
        } = operations;

        const [, {addToast}] = useToasts();

        const [{cartId}, {createCart, removeCart}] = useCartContext();
        const apolloClient = useApolloClient();
        const [fetchCartId] = useMutation(createCartMutation);

        let [
            placeOrder,
            {
                data: placeOrderData,
                error: placeOrderError,
                loading: placeOrderLoading,
                called: placeOrderCalled
            }
        ] = useMutation(placeEwayOrderMutation);

        const [orderButtonPress, setOrderButtonPress] = useState();

          // Add the useLazyQuery for Eway payment URL
        const [fetchEwayUrl, { data: ewayData, loading: ewayLoading, error: ewayError }] = useLazyQuery(EWAY_URL_OPERATIONS.getEwayUrl);

        const [
            restoreQuote,
            {
                data: restoreQuoteData,
                error: restoreQuoteError,
                loading: restoreQuoteLoading,
                called: restoreQuoteCalled
            }
        ] = useMutation(restoreQuoteMutation);

        let [
            getOrderDetails,
            {
                data: orderDetailsData,
                loading: orderDetailsLoading
            }
        ] = useLazyQuery(getOrderDetailsQuery, {
            fetchPolicy: 'no-cache'
        });

        const handlePlaceOrder = useCallback(async () => {
            //alert('testt----1111');
            setOrderButtonPress(true);
            getOrderDetails({
                variables: {
                    cartId
                }
            });
        }, [cartId, getOrderDetails, setOrderButtonPress]);

        useEffect(() => {
            async function placeOrderAndCleanup() {
                try {

                    //alert('testt----22222222222222');
                    // const applicationName = 'Venia UI - Peregrine';
                    // const applicationVersion = veniaPjson.version + ' - ' + peregrinePjson.version;
                    // const pluginVersion = modulePjson.version;
                    setOrderButtonPress(false);
                    //alert('testt----3333333---tetttt');

                    // Fetch eWay payment URL after placing the order
                    fetchEwayUrl({ variables: { cart_id: cartId } });
                    //alert('testt----3333333---fetvhhhh urlll');
                    
                    if (ewayData && ewayData.ewayPaymentRequestData) {
                        const paymentUrl = ewayData.ewayPaymentRequestData.eway_payment_url;
                        //alert(paymentUrl);
                        if (paymentUrl) {
                            window.location.href = paymentUrl;
                        }
                    }
                    // useEffect(() => {
                    //     if (ewayData && ewayData.ewayPaymentRequestData) {
                    //         const paymentUrl = ewayData.ewayPaymentRequestData.eway_payment_url;
                    //         if (paymentUrl) {
                    //             window.location.href = paymentUrl;
                    //         }
                    //     }
                    // }, [ewayData]);
                    if (data && data.cart && data.cart.selected_payment_method.title !=  'Credit Card - Eway') {
                        await placeOrder({
                            variables: {
                                cartId
                            }
                        });
                    }
                    //alert('testt----44444444');
                    //return window.location = 'https://secure-au.sandbox.ewaypayments.com/sharedpage/sharedpayment?AccessCode=F9802MP5vibDCMf-qi_9c_CMkl3CdElZI-NA6Yo14qv9xM_w6sThChYpqG70DUbI-yoB70PuzUr4HFJUXRzGHJUFbE6i5ljMeiKNp6i8rtWDEoHLcTZ6c3Epaqm0udXtCM6wvNA2e6qoSxmAuKWmJDlfMfw==';
                    // if (result) {
                    //     const orderData = result.data;
                    //     const orderMultisafepayUrlData =
                    //         (orderData && orderData.placeOrder.order.eway_payment_url) || null;

                    //     if (orderMultisafepayUrlData
                    //         && (orderMultisafepayUrlData.payment_url || orderMultisafepayUrlData.error)
                    //     ) {
                    //         const {
                    //             error: paymentErrors,
                    //             payment_url: paymentRedirectUrl
                    //         } = orderMultisafepayUrlData;

                    //         if (!paymentErrors && paymentRedirectUrl !== '') {
                    //             await removeCart();
                    //             await clearCartDataFromCache(apolloClient);
                    //             await createCart({
                    //                 fetchCartId
                    //             });

                    //             //
                    //         } else {
                    //             if (paymentErrors) {
                    //                 const restoredQuoteData = await restoreQuote({
                    //                     variables: {
                    //                         cartId
                    //                     }
                    //                 });

                    //                 if (restoredQuoteData) {
                    //                     addToast({
                    //                         type: 'error',
                    //                         icon: errorIcon,
                    //                         message: paymentErrors,
                    //                         dismissable: true,
                    //                         timeout: 7000
                    //                     });

                    //                     if (process.env.NODE_ENV !== 'production') {
                    //                         console.error(paymentErrors);
                    //                     }
                    //                     resetReviewOrderButtonClicked();
                    //                     setCheckoutStep(CHECKOUT_STEP.PAYMENT);
                    //                 }
                    //             }
                    //         }
                    //     } else {
                    //         await removeCart();
                    //         await clearCartDataFromCache(apolloClient);
                    //         await createCart({
                    //             fetchCartId
                    //         });
                    //     }
                    // }
                } catch (err) {
                      //alert(err);
                    console.error(
                        'An error occurred during when placing the order',
                        err
                    );
                    resetReviewOrderButtonClicked();
                    setCheckoutStep(CHECKOUT_STEP.PAYMENT);
                }
            }

            if (orderDetailsData && orderButtonPress) {
                placeOrderAndCleanup();
            }
        }, [
            apolloClient,
            cartId,
            createCart,
            fetchCartId,
            orderDetailsData,
            placeOrder,
            placeOrderCalled,
            removeCart,
            orderButtonPress,
            setOrderButtonPress,
            placeOrderData,
            addToast,
            errorIcon,
            resetReviewOrderButtonClicked,
            setCheckoutStep,
            ewayData
        ]);

        const orderMultisafepayUrlData =
            (placeOrderData && placeOrderData.placeOrder.order.eway_payment_url) || null;

        if (orderMultisafepayUrlData && (orderMultisafepayUrlData.payment_url || orderMultisafepayUrlData.error)) {
            return restoreQuoteData && !restoreQuoteLoading ?
                Object.assign({}, result, {isLoading: false, orderNumber: null, handlePlaceOrder: handlePlaceOrder})
                : Object.assign({}, result, {isLoading: true, orderNumber: null, handlePlaceOrder: handlePlaceOrder});
        }

        return {
            ...result,
            handlePlaceOrder,
            isLoading,
            orderDetailsData,
            orderDetailsLoading,
            orderNumber:
                (placeOrderData && placeOrderData.placeOrder.order.order_number) ||
                null,
            placeOrderLoading,
            setCheckoutStep,
            resetReviewOrderButtonClicked
        };
    }
}

export default wrapUseCheckoutPage;