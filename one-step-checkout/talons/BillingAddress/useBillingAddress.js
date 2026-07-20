import { useCallback, useEffect, useMemo, useRef } from 'react';
import { useFormState, useFormApi } from 'informed';
import { useQuery, useApolloClient, useMutation } from '@apollo/client';
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import { getInitialValues } from '../ShippingInformation/useAddressForm';
import DEFAULT_OPERATIONS from './billingAddress.gql';
import { useAmOscContext } from '../../context';
import {
    deserializeCustomAttributes,
    formatAddress,
    getRegion
} from '../../utils';

export const useBillingAddress = props => {
    const {
        resetShouldSubmit,
        shouldSubmit,
        onBillingAddressChangedError,
        onBillingAddressChangedSuccess
    } = props;

    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);

    const {
        getBillingAddressQuery,
        getShippingAddressQuery,
        getIsBillingAddressSameQuery,
        setBillingAddressMutation
    } = operations;

    const client = useApolloClient();
    const blockRef = useRef();
    const [{ checkoutFields }] = useAmOscContext();
    const formState = useFormState();
    const { validate: validateBillingAddressForm } = useFormApi();
    const [{ cartId }] = useCartContext();

    const { data: shippingAddressData } = useQuery(getShippingAddressQuery, {
        skip: !cartId,
        variables: { cartId }
    });

    const { data: isBillingAddressSameData } = useQuery(
        getIsBillingAddressSameQuery,
        { skip: !cartId, variables: { cartId } }
    );

    const { data: billingAddressData } = useQuery(getBillingAddressQuery, {
        skip: !cartId,
        variables: { cartId }
    });

    const [
        updateBillingAddress,
        {
            error: billingAddressMutationError,
            called: billingAddressMutationCalled,
            loading: billingAddressMutationLoading
        }
    ] = useMutation(setBillingAddressMutation);

    const shippingAddressCountry = shippingAddressData
        ? shippingAddressData.cart.shippingAddresses[0].country.code
        : DEFAULT_COUNTRY_CODE;
    const isBillingAddressSame = formState.values.isBillingAddressSame;

    const initialValues = useMemo(() => {
        const isBillingAddressSame = isBillingAddressSameData
            ? isBillingAddressSameData.cart.isBillingAddressSame
            : true;

        let billingAddress = {
            country_id: shippingAddressCountry
        };
        /**
         * If the user wants billing address same as shipping address, do
         * not auto fill the fields.
         */
        if (isBillingAddressSame) {
            return { isBillingAddressSame, ...billingAddress };
        } else if (billingAddressData) {
            // The user does not want the billing address to be the same.
            // Attempt to pre-populate the form if a billing address is already set.
            if (billingAddressData.cart.billingAddress) {
                const {
                    // eslint-disable-next-line no-unused-vars
                    __typename,
                    ...rawBillingAddress
                } = billingAddressData.cart.billingAddress;
                billingAddress = getInitialValues(rawBillingAddress);
            }
        }

        return {
            isBillingAddressSame,
            ...billingAddress,
            region: getRegion(billingAddress.region)
        };
    }, [isBillingAddressSameData, billingAddressData, shippingAddressCountry]);

    /**
     * Helpers
     */

    /**
     * This function sets the boolean isBillingAddressSame
     * in cache for future use. We use cache because there
     * is no way to save this on the cart in remote.
     */
    const setIsBillingAddressSameInCache = useCallback(() => {
        client.writeQuery({
            query: getIsBillingAddressSameQuery,
            data: {
                cart: {
                    __typename: 'Cart',
                    id: cartId,
                    isBillingAddressSame
                }
            }
        });
    }, [client, cartId, getIsBillingAddressSameQuery, isBillingAddressSame]);

    /**
     * This function sets the billing address on the cart using the
     * shipping address.
     */
    const setShippingAddressAsBillingAddress = useCallback(() => {
        const shippingAddress = shippingAddressData
            ? getInitialValues(shippingAddressData.cart.shippingAddresses[0])
            : {};

        const address = formatAddress(shippingAddress);

        updateBillingAddress({
            variables: {
                cartId,
                address: {
                    ...address,
                    custom_attributes: deserializeCustomAttributes(
                        address.custom_attributes
                    )
                },
                sameAsShipping: true
            }
        });
    }, [updateBillingAddress, shippingAddressData, cartId]);

    /**
     * This function sets the billing address on the cart using the
     * information from the form.
     */
    const setBillingAddress = useCallback(() => {
        const { custom_attributes } = formState.values;

        const address = formatAddress(formState.values);

        updateBillingAddress({
            variables: {
                cartId,
                address: {
                    ...address,
                    custom_attributes: deserializeCustomAttributes(
                        custom_attributes
                    )
                },
                sameAsShipping: false
            }
        });
    }, [formState.values, updateBillingAddress, cartId]);

    /**
     * Effects
     */

    /**
     *
     * User has clicked the update button
     */
    useEffect(() => {
        try {
            if (shouldSubmit) {
                validateBillingAddressForm();

                const hasErrors = Object.keys(formState.errors).length;

                if (!hasErrors) {
                    if (isBillingAddressSame) {
                        setShippingAddressAsBillingAddress();
                    } else {
                        setBillingAddress();
                    }
                    setIsBillingAddressSameInCache();
                } else {
                    if (blockRef.current) {
                        blockRef.current.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                    throw new Error('Errors in the billing address form');
                }
            }
        } catch (err) {
            if (process.env.NODE_ENV !== 'production') {
                console.error(err);
            }
            onBillingAddressChangedError();
        }
    }, [
        shouldSubmit,
        isBillingAddressSame,
        setShippingAddressAsBillingAddress,
        setBillingAddress,
        setIsBillingAddressSameInCache,
        onBillingAddressChangedError,
        onBillingAddressChangedSuccess,
        validateBillingAddressForm,
        formState.errors,
        blockRef
    ]);

    /**
     * Billing address mutation has completed
     */
    useEffect(() => {
        try {
            const billingAddressMutationCompleted =
                billingAddressMutationCalled && !billingAddressMutationLoading;

            if (
                billingAddressMutationCompleted &&
                !billingAddressMutationError
            ) {
                onBillingAddressChangedSuccess();
            }

            if (
                billingAddressMutationCompleted &&
                billingAddressMutationError
            ) {
                /**
                 * Billing address save mutation is not successful.
                 * Reset update button clicked flag.
                 */
                throw new Error('Billing address mutation failed');
            }
        } catch (err) {
            if (process.env.NODE_ENV !== 'production') {
                console.error(err);
            }
            resetShouldSubmit();
            onBillingAddressChangedError();
        }
    }, [
        billingAddressMutationError,
        billingAddressMutationCalled,
        billingAddressMutationLoading,
        onBillingAddressChangedError,
        onBillingAddressChangedSuccess,
        resetShouldSubmit
    ]);

    const errors = useMemo(
        () =>
            new Map([
                ['setBillingAddressMutation', billingAddressMutationError]
            ]),
        [billingAddressMutationError]
    );

    return {
        fields: checkoutFields,
        errors,
        isBillingAddressSame,
        initialValues,
        blockRef
    };
};
