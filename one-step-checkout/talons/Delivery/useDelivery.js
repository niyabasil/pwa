import { useAmOscContext } from '../../context';
import { useCallback, useEffect, useMemo, useRef } from 'react';
import moment from 'moment';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from './delivery.gql';
import { useMutation } from '@apollo/client';
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import debounce from 'lodash.debounce';
import { useIntl } from 'react-intl';

const FORM_KEY = 'DELIVERY';
const FORMAT = 'YYYY-MM-DD';

export const useDelivery = (props = {}) => {
    const {
        checkoutInformationData: { amasty_delivery_date }
    } = props;
    const initValuesRef = useRef(amasty_delivery_date);
    const { locale } = useIntl();
    const formatL = moment.localeData(locale).longDateFormat('L');

    const [
        {
            amasty_checkout_delivery_date_enabled: isEnabled,
            amasty_checkout_delivery_date_date_required,
            amasty_checkout_delivery_date_delivery_comment_enable,
            amasty_checkout_delivery_date_delivery_comment_default,
            amasty_checkout_additional_configuration,
            shouldSubmit
        },
        {
            setIsUpdating,
            setSectionError,
            setSectionCompleted,
            resetSectionCompleted
        }
    ] = useAmOscContext();

    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const { updateDeliveryInformationMutation } = operations;

    const [{ cartId }] = useCartContext();
    const formApiRef = useRef(null);

    const [updateDeliveryInfo, { loading }] = useMutation(
        updateDeliveryInformationMutation,
        {
            onCompleted: () => setSectionCompleted(FORM_KEY)
        }
    );

    const {
        amasty_checkout_delivery_date_available_hours,
        amasty_checkout_delivery_date_available_days
    } = amasty_checkout_additional_configuration;

    const availableHours = useMemo(
        () => JSON.parse(amasty_checkout_delivery_date_available_hours),
        [amasty_checkout_delivery_date_available_hours]
    );

    const yesterday = moment().subtract(1, 'day');

    const validateDays = useCallback(
        current => {
            const availableDays = JSON.parse(
                amasty_checkout_delivery_date_available_days
            );
            return (
                current.isAfter(yesterday) &&
                (!availableDays.length || availableDays.includes(current.day()))
            );
        },
        [amasty_checkout_delivery_date_available_days, yesterday]
    );

    const setError = useCallback(error => setSectionError([FORM_KEY, error]), [
        setSectionError
    ]);

    const setDeliveryInfo = useCallback(
        async formValues => {
            try {
                if (formValues.date && cartId) {
                    await updateDeliveryInfo({
                        variables: {
                            cartId,
                            ...formValues,
                            date: moment(formValues.date, formatL).format(
                                FORMAT
                            )
                        }
                    });
                }
            } catch (e) {
                setError(e);
            }
        },
        [updateDeliveryInfo, cartId, setError, formatL]
    );

    const debouncedFormChange = useMemo(
        () => debounce(formValues => setDeliveryInfo(formValues), 500),
        [setDeliveryInfo]
    );

    useEffect(() => {
        if (shouldSubmit && isEnabled) {
            try {
                const { validate, getState } = formApiRef.current || {};

                validate();
                const { errors } = getState();
                const hasErrors = Object.keys(errors).length;

                if (!hasErrors) {
                    return setSectionCompleted(FORM_KEY);
                } else {
                    throw new Error('Errors in the delivery form');
                }
            } catch (e) {
                setError(e);
            }
        }
    }, [shouldSubmit, formApiRef, setError, isEnabled, setSectionCompleted]);

    const onDateChange = useCallback(
        value => {
            const { setValue } = formApiRef.current;
            setValue('date', value.format(formatL));
        },
        [formApiRef, formatL]
    );

    useEffect(() => {
        setIsUpdating(loading);
    }, [loading, setIsUpdating]);

    useEffect(() => {
        if (isEnabled) {
            resetSectionCompleted(FORM_KEY);
        }
    }, [isEnabled, resetSectionCompleted]);

    const optionsFormProps = useMemo(
        () => ({
            getApi: api => (formApiRef.current = api),
            onValueChange: debouncedFormChange,
            initialValues: {
                ...initValuesRef.current,
                date: initValuesRef.current.date
                    ? moment(initValuesRef.current.date, FORMAT).format(formatL)
                    : '',
                time: initValuesRef.current.time
                    ? initValuesRef.current.time.value
                    : ''
            }
        }),
        [formApiRef, initValuesRef, debouncedFormChange, formatL]
    );

    return {
        isEnabled,
        isDateRequired: amasty_checkout_delivery_date_date_required,
        isCommentEnabled: amasty_checkout_delivery_date_delivery_comment_enable,
        commentPlaceholder: amasty_checkout_delivery_date_delivery_comment_default,
        availableHours,
        validateDays,
        optionsFormProps,
        onDateChange
    };
};
