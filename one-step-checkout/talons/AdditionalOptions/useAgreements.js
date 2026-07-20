import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from './additionalOptions.gql';
import { useQuery } from '@apollo/client';
import { useCallback, useEffect, useState } from 'react';
import { useFormApi } from 'informed';
import { useAmOscContext } from '../../context';

const FORM_KEY = 'AGREEMENT';

export const useAgreements = (props = {}) => {
    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const [activeItem, setActiveItem] = useState(null);
    const formApi = useFormApi();

    const [
        { shouldSubmit },
        { setSectionError, setSectionCompleted }
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
