import { useCallback, useEffect, useMemo, useRef } from 'react';
import { useAmOscContext } from '../../context';
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from './additionalOptions.gql';
import { useMutation } from '@apollo/client';
import debounce from 'lodash.debounce';
import { useUserContext } from '@magento/peregrine/lib/context/user';

const FORM_KEY = 'ADDITIONAL_FIELDS';

export const useAdditionalOptions = (props = {}) => {
    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const { saveAdditionalFieldsMutation } = operations;

    const [{ cartId }] = useCartContext();
    const [{ isSignedIn }] = useUserContext();
    const additionalFieldsFormApiRef = useRef(null);

    const [
        {
            amasty_checkout_additional_options_discount: isCouponCodeEnabled,
            amasty_checkout_additional_options_newsletter: isNewsletterEnabled,
            amasty_checkout_additional_options_newsletter_checked: isNewsletterChecked,
            amasty_checkout_additional_options_display_agreements: agreementsPosition,
            amasty_checkout_additional_options_comment: isOrderCommentEnabled,
            amasty_checkout_options_enable_agreements: isAgreementsEnabled,
            amasty_checkout_additional_options_create_account: createAccountOption,
            amasty_checkout_additional_options_create_account_checked: isCreateAccountChecked,
            shouldSubmit,
            isGuestEmailAvailable
        },
        {
            setIsUpdating,
            setSectionError,
            setSectionCompleted,
            resetSectionCompleted
        }
    ] = useAmOscContext();

    const [saveAdditionalFields, { loading, called }] = useMutation(
        saveAdditionalFieldsMutation,
        {
            onCompleted: () => setSectionCompleted(FORM_KEY)
        }
    );

    const setError = useCallback(err => setSectionError([FORM_KEY, err]), [
        setSectionError
    ]);

    const setAdditionalFields = useCallback(
        async formValues => {
            try {
                if (cartId) {
                    await saveAdditionalFields({
                        variables: {
                            additionalFields: {
                                cart_id: cartId,
                                ...formValues
                            }
                        }
                    });
                }
            } catch (e) {
                setError(e);
            }
        },
        [cartId, saveAdditionalFields, setError]
    );

    const debouncedFormChange = useMemo(
        () => debounce(formValues => setAdditionalFields(formValues), 500),
        [setAdditionalFields]
    );

    useEffect(() => {
        const { validate: additionalFieldsValidate, getState } =
            additionalFieldsFormApiRef.current || {};

        if (shouldSubmit) {
            try {
                additionalFieldsValidate();
                const { errors, values } = getState();

                const hasErrors = Object.keys(errors).length;

                if (!hasErrors) {
                    return !called
                        ? setAdditionalFields(values)
                        : setSectionCompleted(FORM_KEY);
                } else {
                    throw new Error('Errors in the additional fields');
                }
            } catch (e) {
                setError(e);
            }
        }
    }, [
        shouldSubmit,
        additionalFieldsFormApiRef,
        setSectionCompleted,
        setError,
        called,
        setAdditionalFields
    ]);

    const additionalFieldsFormProps = {
        getApi: api => (additionalFieldsFormApiRef.current = api),
        onValueChange: debouncedFormChange
    };

    useEffect(() => {
        setIsUpdating(loading);
    }, [loading, setIsUpdating]);

    useEffect(() => {
        resetSectionCompleted(FORM_KEY);
    }, [resetSectionCompleted]);

    return {
        isCouponCodeEnabled,
        isNewsletterEnabled,
        isNewsletterChecked,
        isOrderCommentEnabled,
        isAgreementsEnabled:
            isAgreementsEnabled && agreementsPosition === 'order_totals',
        isCreateAccountEnabled:
            !isSignedIn && createAccountOption === 1 && isGuestEmailAvailable, // 1 => After Placing an Order
        isCreateAccountChecked,
        additionalFieldsFormProps
    };
};
