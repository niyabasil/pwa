import DEFAULT_OPERATIONS from './shippingInformation.gql';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import { useMutation } from '@apollo/client';
import { useCallback, useEffect, useMemo, useRef } from 'react';
import debounce from 'lodash.debounce';
import { useAmOscContext } from '../../context';
import {
    formatAddress,
    deserializeCustomAttributes,
    getRequiredFields
} from '../../utils';

const FORM_KEY = 'ADDRESS_FORM';

export const useAsyncAddressForm = (props = {}) => {
    const {
        checkoutInformationData: { shipping_addresses }
    } = props;

    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const { setShippingAddressMutation } = operations;

    const formApiRef = useRef();
    const prevAddressRef = useRef(shipping_addresses[0]);
    const [{ cartId }] = useCartContext();
    const [
        { checkoutFields, shouldSubmit },
        { setIsUpdating, setSectionError, setSectionCompleted }
    ] = useAmOscContext();

    const setError = useCallback(error => setSectionError([FORM_KEY, error]), [
        setSectionError
    ]);

    const setCompleted = useCallback(() => setSectionCompleted(FORM_KEY), [
        setSectionCompleted
    ]);

    // mutations

    const [setShippingAddress, { error, setGuestAddressLoading }] = useMutation(
        setShippingAddressMutation,
        {
            onCompleted: setCompleted
        }
    );

    const requiredFields = useMemo(() => getRequiredFields(checkoutFields), [
        checkoutFields
    ]);

    const handleSubmit = useCallback(
        async formValues => {
            const { validate } = formApiRef.current;
            try {
                await setShippingAddress({
                    variables: {
                        cartId,
                        address: {
                            ...formValues,
                            custom_attributes: deserializeCustomAttributes(
                                formValues.custom_attributes
                            )
                        }
                    }
                });
                setIsUpdating(false);
            } catch (error) {
                validate();
                setIsUpdating(false);
                setError(error);
            }
        },
        [setShippingAddress, setIsUpdating, formApiRef, cartId, setError]
    );

    const handleChange = useCallback(
        values => {
            const { validate, getState } = formApiRef.current;
            const { errors } = getState();

            const currentAddress = shipping_addresses[0];

            const newAddress = formatAddress({
                ...currentAddress,
                ...values
            });

            const { country_id, region } = values;
            const isCountryChanged =
                currentAddress.country &&
                country_id !== currentAddress.country.code;
            const isFormFilled =
                requiredFields.every(f => {
                    const [key, subKey] = f.split('.');
                    return subKey
                        ? values[key] && values[key][subKey]
                        : values[f];
                }) && !Object.keys(errors).length;

            if (isCountryChanged || isFormFilled) {
                return region ? handleSubmit(newAddress) : validate();
            }

            setIsUpdating(false);
        },
        [
            setIsUpdating,
            formApiRef,
            shipping_addresses,
            handleSubmit,
            requiredFields
        ]
    );

    const debouncedFormChange = useMemo(() => debounce(handleChange, 500), [
        handleChange
    ]);

    const optionsFormProps = {
        getApi: api => (formApiRef.current = api),
        onValueChange: formValues => {
            setIsUpdating(true);
            debouncedFormChange(formValues);
        }
    };

    useEffect(() => {
        if (shouldSubmit) {
            try {
                const { validate, getState } = formApiRef.current || {};

                validate();
                const { errors } = getState();
                const hasErrors = Object.keys(errors).length;

                if (!hasErrors) {
                    return setSectionCompleted(FORM_KEY);
                } else {
                    throw new Error('Errors in the address form');
                }
            } catch (e) {
                return setError(e);
            }
        }
    }, [shouldSubmit, formApiRef, setSectionCompleted, setError]);

    const errors = useMemo(
        () => new Map([['setGuestShippingMutation', error]]),
        [error]
    );

    const shippingData = useMemo(() => {
        const address = { ...prevAddressRef.current };

        const initialFormValues = Object.entries(address).reduce(
            (res, [key, value]) => {
                const val = Array.isArray(value)
                    ? value.filter(f => f !== '-').join()
                    : value;
                if (val && val !== '-') {
                    res[key] = value;
                }
                return res;
            },
            {}
        );

        return initialFormValues;
    }, [prevAddressRef]);

    return {
        errors,
        isSaving: setGuestAddressLoading,
        handleSubmit,
        optionsFormProps,
        shippingData
    };
};
