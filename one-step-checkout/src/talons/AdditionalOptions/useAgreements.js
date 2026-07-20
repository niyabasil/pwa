import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from './additionalOptions.gql';
import { useQuery } from '@apollo/client';
import { useCallback, useEffect, useState } from 'react';
import { useFormApi, useFormState } from 'informed';
import { useAmOscContext } from '../../context';

const FORM_KEY = 'AGREEMENT';

export const useAgreements = (props = {}) => {
    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const [activeItem, setActiveItem] = useState(null);
    const formApi = useFormApi();
    const formState = useFormState();

    const [
        { shouldSubmit },
        { setSectionError, setSectionCompleted, setSectionIsDone }
    ] = useAmOscContext();
    const setError = useCallback(err => setSectionError([FORM_KEY, err]), [
        setSectionError
    ]);

    const { getCheckoutAgreementsQuery } = operations;

    const { data } = useQuery(getCheckoutAgreementsQuery, {
        fetchPolicy: 'cache-and-network',
        nextFetchPolicy: 'cache-first'
    });

    const { checkoutAgreements } = data || {};

    const handleCloseModal = useCallback(() => {
        setActiveItem(null);
    }, [setActiveItem]);

    const handleOpenModal = useCallback(item => setActiveItem(item), [
        setActiveItem
    ]);

    // Reactively track agreement checkbox state so PayPal (and other
    // payment flows that bypass the Place Order button) can read isDoneMap.
    useEffect(() => {
        if (!checkoutAgreements) return;

        const manualAgreements = checkoutAgreements.filter(
            a => a.mode === 'MANUAL'
        );

        if (manualAgreements.length === 0) {
            // No manual checkboxes required — mark as done immediately.
            setSectionCompleted(FORM_KEY);
            return;
        }

        const values = formState.values?.agreements || {};
        const allChecked = manualAgreements.every(
            a => !!values[`agreement_${a.agreement_id}`]
        );

        if (allChecked) {
            setSectionCompleted(FORM_KEY);
        } else {
            setSectionIsDone([FORM_KEY, false]);
        }
    }, [formState.values, checkoutAgreements, setSectionCompleted, setSectionIsDone]);

    useEffect(() => {
        const { validate, getState } = formApi;

        if (shouldSubmit) {
            try {
                validate();
                const { errors } = getState();
                const hasErrors = Object.keys(errors).length;

                if (!hasErrors) {
                    return setSectionCompleted(FORM_KEY);
                } else {
                    throw new Error('Errors in the agreement fields');
                }
            } catch (e) {
                setError(e);
            }
        }
    }, [shouldSubmit, setError, formApi, setSectionCompleted]);

    return {
        checkoutAgreements,
        activeItem,
        handleCloseModal,
        handleOpenModal
    };
};
