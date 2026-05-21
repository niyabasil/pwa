import { useCallback, useEffect, useState } from 'react';
import { useQuery, useMutation } from '@apollo/client';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import { useCartContext } from '@magento/peregrine/lib/context/cart';

import DEFAULT_OPERATIONS from './paypalExpress.gql';
import { useToasts } from '@magento/peregrine';
import { useIntl } from 'react-intl';

const defaultMessage = 'Something wrong.Please try again';

export const usePaypalExpress = props => {
    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);

    const {
        getPaypalExpressConfigQuery,
        createPaypalExpressTokenMutation,
        setPaypalExpressDetailsOnCartMutation,
        setBillingAddressMutation
    } = operations;

    const [{ cartId }] = useCartContext();
    const [, { addToast }] = useToasts();
    const { formatMessage } = useIntl();

    const { data } = useQuery(getPaypalExpressConfigQuery);
    const [errorMessage, setErrorMessage] = useState(null);
    const [loadedScript, setLoadedScript] = useState(false);

    const handleLoadScript = useCallback(() => {
        setLoadedScript(true);
    }, []);

    const {
        resetShouldSubmit,
        shouldSubmit,
        onPaymentSuccess,
        onPaymentError
    } = props;

    const [createPaypalExpressToken] = useMutation(
        createPaypalExpressTokenMutation
    );
    const [
        updatePaymentMethod,
        {
            error: paymentMethodMutationError,
            called: paymentMethodMutationCalled,
            loading: paymentMethodMutationLoading
        }
    ] = useMutation(setPaypalExpressDetailsOnCartMutation);

    const [updateBillingAddress] = useMutation(setBillingAddressMutation);

    useEffect(() => {
        if (shouldSubmit) {
            resetShouldSubmit();
        }
    }, [shouldSubmit]);

    useEffect(() => {
        const paymentMethodMutationCompleted =
            paymentMethodMutationCalled && !paymentMethodMutationLoading;

        if (paymentMethodMutationCompleted && !paymentMethodMutationError) {
            onPaymentSuccess();
        }

        if (paymentMethodMutationCompleted && paymentMethodMutationError) {
            onPaymentError();
        }
    }, [
        paymentMethodMutationError,
        paymentMethodMutationLoading,
        paymentMethodMutationCalled,
        onPaymentSuccess,
        onPaymentError,
        resetShouldSubmit
    ]);

    const onError = error => {
        console.log(error, 'pay failed');
    };

    const payment = async (resolve, reject) => {
        try {
            const resultToken = await createPaypalExpressToken({
                variables: {
                    cartId,
                    code: 'paypal_express',
                    urls: {
                        return_url: 'paypal/action/return.html',
                        cancel_url: 'paypal/action/cancel.html'
                    }
                }
            });
            if (
                resultToken &&
                resultToken.data &&
                resultToken.data.createPaypalExpressToken &&
                resultToken.data.createPaypalExpressToken.token
            ) {
                setErrorMessage(null);
                return resolve(resultToken.data.createPaypalExpressToken.token);
            }
            reject(new Error(''));
        } catch (e) {
            setErrorMessage(e.message);
            reject(e);
        }
    };

    const onAuthorize = async data => {
        if (!data || !data.paymentToken || !data.payerID) {
            addToast({
                type: 'error',
                message: formatMessage({
                    id: 'checkoutPage.errorPaypal',
                    defaultMessage
                }),
                timeout: 3000
            });
            return;
        }
        try {
            // Step 1: Set PayPal payment method first.
            // Magento uses the token + payerID to fetch the customer's shipping
            // address from PayPal's API and stores it on the cart.
            await updatePaymentMethod({
                variables: {
                    cartId,
                    token: data.paymentToken,
                    payerID: data.payerID
                }
            });
            // Step 2: Now set billing address as same_as_shipping.
            // The shipping address is already populated from PayPal's response
            // above, so this correctly copies it into the billing address.
            // Previously this ran BEFORE updatePaymentMethod, which caused
            // billing/shipping address to appear as dashes in the Magento
            // backend for customers who had no prior shipping address on cart.
            await updateBillingAddress({
                variables: {
                    cartId,
                    sameAsShipping: true
                }
            });
        } catch (error) {
            let message = '';
            if (error.graphQLErrors && error.graphQLErrors[0]) {
                if (error.graphQLErrors[0].debugMessage) {
                    message = error.graphQLErrors[0].debugMessage;
                } else if (error.graphQLErrors[0].message) {
                    message = error.graphQLErrors[0].message;
                }
            }
            addToast({
                type: 'error',
                message: formatMessage({
                    id: 'checkoutPage.errorPaypal',
                    defaultMessage: message ? message : defaultMessage
                }),
                timeout: 3000
            });
        }
    };

    return {
        paypal_env:
            data && data.storeConfig && data.storeConfig.paypal_sandbox === '1'
                ? 'sandbox'
                : 'production',
        paypal_merchant_id:
            data && data.storeConfig && data.storeConfig.paypal_merchant_id,
        onAuthorize,
        payment,
        onError,
        errorMessage,
        loadedScript,
        handleLoadScript
    };
};
