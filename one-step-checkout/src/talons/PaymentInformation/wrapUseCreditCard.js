import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from '@magento/peregrine/lib/talons/CheckoutPage/PaymentInformation/creditCard.gql';
import { useCallback, useEffect, useMemo, useState } from 'react';
import { useApolloClient, useMutation } from '@apollo/client';
import { useFormApi, useFormState } from 'informed';
import { useCartContext } from '@magento/peregrine/lib/context/cart';

const wrapUseCreditCard = () => props => {
    const {
        onSuccess,
        onReady,
        onError,
        shouldSubmit,
        resetShouldSubmit
    } = props;

    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const {
        getPaymentNonceQuery,
        setCreditCardDetailsOnCartMutation
    } = operations;

    /**
     * Definitions
     */

    const [isDropinLoading, setDropinLoading] = useState(true);
    const [shouldRequestPaymentNonce, setShouldRequestPaymentNonce] = useState(
        false
    );
    const [shouldTeardownDropin, setShouldTeardownDropin] = useState(false);
    /**
     * `stepNumber` depicts the state of the process flow in credit card
     * payment flow.
     *
     * `0` No call made yet
     * `1` Billing address mutation intiated
     * `2` Braintree nonce requsted
     * `3` Payment information mutation intiated
     * `4` All mutations done
     */
    const [stepNumber, setStepNumber] = useState(0);

    const client = useApolloClient();
    const formState = useFormState();
    const { validate: validateBillingAddressForm } = useFormApi();
    const [{ cartId }] = useCartContext();

    const isLoading = isDropinLoading || (stepNumber >= 1 && stepNumber <= 3);

    const [
        updateCCDetails,
        {
            error: ccMutationError,
            called: ccMutationCalled,
            loading: ccMutationLoading
        }
    ] = useMutation(setCreditCardDetailsOnCartMutation);

    /**
     * This function sets the payment nonce details in the cache.
     * We use cache because there is no way to save this information
     * on the cart in the remote.
     *
     * We do not save the nonce code because it is a PII.
     */
    const setPaymentDetailsInCache = useCallback(
        braintreeNonce => {
            /**
             * We dont save the nonce code due to PII,
             * we only save the subset of details.
             */
            const { details, description, type } = braintreeNonce;
            client.writeQuery({
                query: getPaymentNonceQuery,
                data: {
                    cart: {
                        __typename: 'Cart',
                        id: cartId,
                        paymentNonce: {
                            details,
                            description,
                            type
                        }
                    }
                }
            });
        },
        [cartId, client, getPaymentNonceQuery]
    );

    /**
     * This function saves the nonce code from braintree
     * on the cart along with the payment method used in
     * this case `braintree`.
     */
    const updateCCDetailsOnCart = useCallback(
        braintreeNonce => {
            const { nonce } = braintreeNonce;
            updateCCDetails({
                variables: {
                    cartId,
                    paymentMethod: 'braintree',
                    paymentNonce: nonce
                }
            });
        },
        [updateCCDetails, cartId]
    );

    /**
     * Function to be called by the braintree dropin when the
     * nonce generation is successful.
     */
    const onPaymentSuccess = useCallback(
        braintreeNonce => {
            setPaymentDetailsInCache(braintreeNonce);
            /**
             * Updating payment braintreeNonce and selected payment method on cart.
             */
            updateCCDetailsOnCart(braintreeNonce);
            setStepNumber(3);
        },
        [setPaymentDetailsInCache, updateCCDetailsOnCart]
    );

    /**
     * Function to be called by the braintree dropin when the
     * nonce generation is not successful.
     */
    const onPaymentError = useCallback(
        error => {
            setStepNumber(0);
            setShouldRequestPaymentNonce(false);
            resetShouldSubmit();
            if (onError) {
                onError(error);
            }
        },
        [onError, resetShouldSubmit]
    );

    /**
     * Function to be called by the braintree dropin when the
     * credit card component has loaded successfully.
     */
    const onPaymentReady = useCallback(() => {
        setDropinLoading(false);
        setStepNumber(0);
        if (onReady) {
            onReady();
        }
    }, [onReady]);

    /**
     * Function to be called by braintree dropin when the payment
     * teardown is done successfully before re creating the new dropin.
     */
    const resetShouldTeardownDropin = useCallback(() => {
        setShouldTeardownDropin(false);
    }, []);

    useEffect(() => {
        try {
            if (shouldSubmit) {
                /**
                 * Validate billing address fields and only process with
                 * submit if there are no errors.
                 *
                 * We do this because the user can click Review Order button
                 * without fillig in all fields and the form submission
                 * happens manually. The informed Form component validates
                 * on submission but that only happens when we use the onSubmit
                 * prop. In this case we are using manually submission because
                 * of the nature of the credit card submission process.
                 */
                validateBillingAddressForm();

                const hasErrors = Object.keys(formState.errors).length;

                if (!hasErrors) {
                    setStepNumber(1);
                } else {
                    throw new Error('Errors in the billing address form');
                }
            }
        } catch (err) {
            if (process.env.NODE_ENV !== 'production') {
                console.error(err);
            }
            setStepNumber(0);
            resetShouldSubmit();
            setShouldRequestPaymentNonce(false);
        }
    }, [
        shouldSubmit,
        resetShouldSubmit,
        validateBillingAddressForm,
        formState.errors
    ]);

    /**
     * Step 3 effect
     *
     * Credit card save mutation has completed
     */
    useEffect(() => {
        /**
         * Saved billing address, payment method and payment nonce on cart.
         *
         * Time to call onSuccess.
         */

        try {
            const ccMutationCompleted = ccMutationCalled && !ccMutationLoading;

            if (ccMutationCompleted && !ccMutationError) {
                if (onSuccess) {
                    onSuccess();
                }
                resetShouldSubmit();
                setStepNumber(4);
            }

            if (ccMutationCompleted && ccMutationError) {
                /**
                 * If credit card mutation failed, reset update button clicked so the
                 * user can click again and set `stepNumber` to 0.
                 */
                throw new Error('Credit card nonce save mutation failed.');
            }
        } catch (err) {
            if (process.env.NODE_ENV !== 'production') {
                console.error(err);
            }
            setStepNumber(0);
            resetShouldSubmit();
            setShouldRequestPaymentNonce(false);
            setShouldTeardownDropin(true);
        }
    }, [
        ccMutationCalled,
        ccMutationLoading,
        onSuccess,
        setShouldRequestPaymentNonce,
        resetShouldSubmit,
        ccMutationError
    ]);

    const errors = useMemo(
        () =>
            new Map([['setCreditCardDetailsOnCartMutation', ccMutationError]]),
        [ccMutationError]
    );

    const onBillingAddressChangedSuccess = useCallback(() => {
        setStepNumber(2);
        setShouldRequestPaymentNonce(true);
    }, [setStepNumber, setShouldRequestPaymentNonce]);

    const onBillingAddressChangedError = useCallback(() => {
        setStepNumber(0);
        resetShouldSubmit();
        setShouldRequestPaymentNonce(false);
    }, [setStepNumber, resetShouldSubmit, setShouldRequestPaymentNonce]);

    return {
        errors,
        onPaymentError,
        onPaymentSuccess,
        onPaymentReady,
        isLoading,
        shouldRequestPaymentNonce,
        stepNumber,
        shouldTeardownDropin,
        resetShouldTeardownDropin,
        onBillingAddressChangedSuccess,
        onBillingAddressChangedError
    };
};

export default wrapUseCreditCard;
