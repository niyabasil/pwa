import { useCallback, useEffect, useState, useRef, useMemo } from 'react';
import { useAmOscContext } from '../../context';
import { useAppContext } from '@magento/peregrine/lib/context/app';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from './addressBook.gql';
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import { useUserContext } from '@magento/peregrine/lib/context/user';
import { useMutation, useQuery } from '@apollo/client';
import { deriveErrorMessage } from '@magento/peregrine/lib/util/deriveErrorMessage';

export const useAddressBook = (props = {}) => {
    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const {
        setCustomerAddressOnCartMutation,
        getCustomerAddressesQuery,
        getCustomerCartAddressQuery
    } = operations;

    const [
        {
            amasty_checkout_design_display_shipping_address_in: addressDesign,
            isUpdating
        },
        { setIsUpdating }
    ] = useAmOscContext();

    const addressCount = useRef();
    const [isEditing, setIsEditing] = useState();
    const [showNewAddressForm, setShowNewAddressForm] = useState();
    const [, { closeDrawer, toggleDrawer }] = useAppContext();
    const [{ cartId }] = useCartContext();
    const [{ isSignedIn }] = useUserContext();
    const [activeAddress, setActiveAddress] = useState();
    const [selectedAddress, setSelectedAddress] = useState();

    const handleSuccess = useCallback(() => {
        closeDrawer();
        setIsEditing();
    }, [closeDrawer, setIsEditing]);

    // queries
    const {
        data: customerAddressesData,
        loading: customerAddressesLoading
    } = useQuery(getCustomerAddressesQuery, {
        fetchPolicy: 'cache-and-network',
        nextFetchPolicy: 'cache-first',
        skip: !isSignedIn
    });

    const {
        data: customerCartAddressData,
        loading: customerCartAddressLoading
    } = useQuery(getCustomerCartAddressQuery, {
        fetchPolicy: 'cache-and-network',
        nextFetchPolicy: 'cache-first',
        skip: !isSignedIn
    });

    // mutations

    const [
        setCustomerAddressOnCart,
        {
            error: setCustomerAddressOnCartError,
            loading: setCustomerAddressOnCartLoading
        }
    ] = useMutation(setCustomerAddressOnCartMutation, {
        onCompleted: handleSuccess
    });

    const derivedErrorMessage = useMemo(
        () => deriveErrorMessage([setCustomerAddressOnCartError]),
        [setCustomerAddressOnCartError]
    );

    const customerAddresses = useMemo(
        () =>
            (customerAddressesData &&
                customerAddressesData.customer.addresses) ||
            [],
        [customerAddressesData]
    );

    const isLoading = customerAddressesLoading || customerCartAddressLoading;

    const handleApplyAddress = useCallback(async () => {
        try {
            await setCustomerAddressOnCart({
                variables: {
                    cartId,
                    addressId: selectedAddress
                }
            });
        } catch {
            return;
        }
    }, [cartId, selectedAddress, setCustomerAddressOnCart]);

    const prevSelectedAddress = useRef(selectedAddress);

    const handleEditAddress = useCallback(
        address => {
            setActiveAddress(address);
            toggleDrawer('shippingInformation.edit');
        },
        [toggleDrawer]
    );

    const handleAddAddress = useCallback(() => {
        handleEditAddress();
    }, [handleEditAddress]);

    const handleSelectAddress = useCallback(
        addressId => {
            setSelectedAddress(addressId);
        },
        [setSelectedAddress]
    );

    const handleShowEditArea = useCallback(() => {
        setIsEditing(true);
    }, [setIsEditing]);

    const handleCancel = useCallback(() => {
        setIsEditing();
        setShowNewAddressForm();
    }, [setIsEditing, setShowNewAddressForm]);

    const handleChangeAddress = useCallback(
        ({ target }) => {
            const { value } = target;
            setShowNewAddressForm(value === 'new');
        },
        [setShowNewAddressForm]
    );

    useEffect(() => {
        if (
            customerAddresses.length &&
            customerCartAddressData &&
            !selectedAddress
        ) {
            const { customerCart } = customerCartAddressData;
            const { shipping_addresses: shippingAddresses } = customerCart;
            const primaryCartAddress =
                shippingAddresses.length && shippingAddresses[0];

            const foundSelectedAddress = primaryCartAddress
                ? customerAddresses.find(
                      customerAddress =>
                          customerAddress.street[0] ===
                              primaryCartAddress.street[0] &&
                          customerAddress.firstname ===
                              primaryCartAddress.firstname &&
                          customerAddress.lastname ===
                              primaryCartAddress.lastname
                  )
                : customerAddresses.find(address => address.default_shipping);

            if (foundSelectedAddress) {
                setSelectedAddress(foundSelectedAddress.id);
            } else {
                const newestAddress =
                    customerAddresses[customerAddresses.length - 1];
                setSelectedAddress(newestAddress.id);
            }
        }
    }, [customerAddresses, customerCartAddressData, selectedAddress]);

    useEffect(() => {
        if (customerAddresses.length !== addressCount.current) {
            // Auto-select newly added address when count changes
            if (addressCount.current) {
                const newestAddress =
                    customerAddresses[customerAddresses.length - 1];
                setSelectedAddress(newestAddress.id);
            }

            addressCount.current = customerAddresses.length;
        }
    }, [customerAddresses, addressCount]);

    useEffect(() => {
        if (prevSelectedAddress.current !== selectedAddress) {
            handleApplyAddress();
        }

        prevSelectedAddress.current = selectedAddress;
    }, [selectedAddress, handleApplyAddress, prevSelectedAddress]);

    useEffect(() => setIsUpdating(setCustomerAddressOnCartLoading), [
        setCustomerAddressOnCartLoading,
        setIsUpdating
    ]);

    useEffect(() => {
        setIsEditing(!!activeAddress);
    }, [setIsEditing, activeAddress]);

    return {
        activeAddress,
        customerAddresses,
        errorMessage: derivedErrorMessage,
        isLoading,
        handleAddAddress,
        handleSelectAddress,
        handleEditAddress,
        selectedAddress,
        handleCancel,
        isDropDownView: addressDesign === 1,
        handleSuccess,
        isEditing,
        handleShowEditArea,
        handleChangeAddress,
        showNewAddressForm,
        isUpdating
    };
};
