import { useCallback, useMemo } from 'react';
import { useAmOscContext } from '../../context';
import { useUserContext } from '@magento/peregrine/lib/context/user';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import CUSTOMER_FORM_OPERATIONS from '@magento/peregrine/lib/talons/CheckoutPage/ShippingInformation/AddressForm/customerForm.gql';
import ADDRESS_BOOK_OPERATIONS from './addressBook.gql';
import { useMutation } from '@apollo/client';
import {
    serializeCustomAttributes,
    deserializeCustomAttributes
} from '../../utils';

export const getInitialValues = shippingData => {
    if (!shippingData) {
        return {};
    }

    const { country, country_code, region, custom_attributes } = shippingData;
    const countryCode =
        country_code || (country && country.code) || DEFAULT_COUNTRY_CODE;

    return {
        ...shippingData,
        country_id: countryCode,
        region: {
            region_id: region && (region.region_id || region.code),
            region:
                region &&
                (region.region || (region.label !== '-' ? region.label : ''))
        },
        ...serializeCustomAttributes(custom_attributes)
    };
};

export const useAddressForm = props => {
    const { shippingData, afterSubmit, onCancel, onSuccess } = props;

    const operations = mergeOperations(
        CUSTOMER_FORM_OPERATIONS,
        ADDRESS_BOOK_OPERATIONS,
        props.operations
    );
    const {
        createCustomerAddressMutation,
        updateCustomerAddressMutation,
        getCustomerAddressesQuery,
        getDefaultShippingQuery
    } = operations;

    const [{ checkoutFields }] = useAmOscContext();
    const [{ isSignedIn, currentUser }] = useUserContext();
    const handleSuccess = () => onSuccess && onSuccess();

    // mutations

    const [
        createCustomerAddress,
        { loading: createCustomerAddressLoading }
    ] = useMutation(createCustomerAddressMutation, {
        onCompleted: handleSuccess
    });

    const [
        updateCustomerAddress,
        { loading: updateCustomerAddressLoading }
    ] = useMutation(updateCustomerAddressMutation, {
        onCompleted: handleSuccess
    });

    const isUpdate = shippingData && !!shippingData.city;

    const handleSubmit = useCallback(
        async formValues => {
            const {
                country_id,
                region,
                custom_attributes,
                ...address
            } = formValues;

            if (!isSignedIn) {
                return;
            }

            try {
                const customerAddress = {
                    ...address,
                    region,
                    // Cleans up the street array when values are null or undefined
                    street: address.street.filter(e => e),
                    country_code: country_id,
                    custom_attributes: deserializeCustomAttributes(
                        custom_attributes
                    )
                };

                if (isUpdate) {
                    const { id: addressId } = shippingData;
                    await updateCustomerAddress({
                        variables: {
                            addressId,
                            address: customerAddress
                        },
                        refetchQueries: [{ query: getCustomerAddressesQuery }]
                    });
                } else {
                    await createCustomerAddress({
                        variables: {
                            address: customerAddress
                        },
                        refetchQueries: [
                            { query: getCustomerAddressesQuery },
                            { query: getDefaultShippingQuery }
                        ]
                    });
                }
            } catch {
                return;
            }

            if (afterSubmit) {
                afterSubmit();
            }
        },
        [
            isSignedIn,
            isUpdate,
            shippingData,
            afterSubmit,
            updateCustomerAddress,
            createCustomerAddress,
            getCustomerAddressesQuery,
            getDefaultShippingQuery
        ]
    );

    const handleCancel = useCallback(() => {
        if (onCancel) {
            onCancel();
        }
    }, [onCancel]);

    const initialValues = useMemo(() => {
        const initialData = getInitialValues(shippingData);

        if (isSignedIn) {
            const { email, firstname, lastname } = currentUser;

            return {
                email,
                firstname,
                lastname,
                ...initialData
            };
        }

        return initialData;
    }, [isSignedIn, currentUser, shippingData]);

    return {
        fields: checkoutFields,
        initialValues,
        isUpdate,
        handleSubmit,
        handleCancel,
        isSaving: createCustomerAddressLoading || updateCustomerAddressLoading
    };
};
