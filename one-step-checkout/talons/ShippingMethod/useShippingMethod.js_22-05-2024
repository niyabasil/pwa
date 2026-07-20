import { useMutation, useQuery } from '@apollo/client';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from '@magento/peregrine/lib/talons/CheckoutPage/ShippingMethod/shippingMethod.gql';
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import { useCallback, useEffect, useMemo, useRef } from 'react';
import { useAmOscContext } from '../../context';

const serializeShippingMethod = method => {
    const { carrier_code, method_code } = method;

    return `${carrier_code}_${method_code}`;
};

const deserializeShippingMethod = serializedValue => {
    return serializedValue.split('_');
};

// Sorts available shipping methods by price.
const byPrice = (a, b) => a.amount.value - b.amount.value;

// Adds a serialized property to shipping method objects
// so they can be selected in the radio group.
const addSerializedProperty = shippingMethod => {
    if (!shippingMethod) return shippingMethod;

    const serializedValue = serializeShippingMethod(shippingMethod);

    return {
        ...shippingMethod,
        serializedValue
    };
};

const DEFAULT_SELECTED_SHIPPING_METHOD = null;
const DEFAULT_AVAILABLE_SHIPPING_METHODS = [];
const FORM_KEY = 'SHIPPING_METHOD';

export const useShippingMethod = (props = {}) => {
    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);

    const {
        getSelectedAndAvailableShippingMethodsQuery,
        setShippingMethodMutation
    } = operations;

    const formApiRef = useRef(null);
    const setFormApi = useCallback(api => (formApiRef.current = api), []);

    const [{ cartId }] = useCartContext();
    const [
        {
            amasty_checkout_default_values_shipping_method: defaultShippingMethod
        },
        {
            setIsUpdating,
            setSectionCompleted,
            setSectionError,
            resetSectionCompleted
        }
    ] = useAmOscContext();

    const { data, loading: isLoadingShippingMethods } = useQuery(
        getSelectedAndAvailableShippingMethodsQuery,
        {
            fetchPolicy: 'cache-and-network',
            nextFetchPolicy: 'cache-first',
            skip: !cartId,
            variables: { cartId }
        }
    );

    const [
        setShippingMethodCall,
        { error: setShippingMethodError, loading: isSettingShippingMethod }
    ] = useMutation(setShippingMethodMutation, {
        onCompleted: () => setSectionCompleted(FORM_KEY)
    });

    const derivedPrimaryShippingAddress = useMemo(
        () =>
            data &&
            data.cart.shipping_addresses &&
            data.cart.shipping_addresses.length
                ? data.cart.shipping_addresses[0]
                : null,
        [data]
    );

    const shippingMethods = useMemo(() => {
        if (!derivedPrimaryShippingAddress)
            return DEFAULT_AVAILABLE_SHIPPING_METHODS;

        const rawShippingMethods =
            derivedPrimaryShippingAddress.available_shipping_methods;
        const shippingMethodsByPrice = [...rawShippingMethods].sort(byPrice);

        return shippingMethodsByPrice.map(addSerializedProperty);
    }, [derivedPrimaryShippingAddress]);

    const selectedShippingMethod = useMemo(
        () =>
            derivedPrimaryShippingAddress
                ? addSerializedProperty(
                      derivedPrimaryShippingAddress.selected_shipping_method
                  )
                : DEFAULT_SELECTED_SHIPPING_METHOD,
        [derivedPrimaryShippingAddress]
    );

    const setShippingMethod = useCallback(
        async value => {
            const [carrierCode, methodCode] = deserializeShippingMethod(value);

            try {
                setIsUpdating(true);

                await setShippingMethodCall({
                    variables: {
                        cartId,
                        shippingMethod: {
                            carrier_code: carrierCode,
                            method_code: methodCode
                        }
                    }
                });
            } catch (e) {
                return setSectionError([FORM_KEY, e]);
            }

            setIsUpdating(false);
        },
        [cartId, setShippingMethodCall, setIsUpdating, setSectionError]
    );

    const handleChange = useCallback(
        e => {
            const { value } = e.target;
            setShippingMethod(value);
        },
        [setShippingMethod]
    );

    useEffect(() => {
        const formApi = formApiRef.current;

        if (selectedShippingMethod) {
            setSectionCompleted(FORM_KEY);
        } else {
            const availableMethods = shippingMethods.filter(m => m.available);

            if (availableMethods.length && formApi) {
                const nextSelectedShippingMethod =
                    availableMethods.find(
                        m => m.serializedValue === defaultShippingMethod
                    ) || availableMethods[0];
                const { serializedValue } = nextSelectedShippingMethod;
                formApi.setValue('shipping_method', serializedValue);
                setShippingMethod(serializedValue);
            }
        }
    }, [
        selectedShippingMethod,
        defaultShippingMethod,
        setShippingMethod,
        shippingMethods,
        formApiRef,
        setSectionCompleted,
        resetSectionCompleted
    ]);

    const errors = useMemo(
        () => new Map([['setShippingMethod', setShippingMethodError]]),
        [setShippingMethodError]
    );

    return {
        shippingMethods,
        errors,
        handleChange,
        isLoading: isLoadingShippingMethods || isSettingShippingMethod,
        selectedShippingMethod,
        setFormApi
    };
};
