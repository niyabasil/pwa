import { useCallback, useMemo, useReducer } from 'react';

import withLogger from '@magento/peregrine/lib/util/withLogger';
import { storeActions } from '../store/index';
import { reducersCompare } from '../reducers';

const { reducer, initialState } = reducersCompare;

const wrappedReducer = withLogger(reducer);

export const useCompareState = () => {
    const {
        createCompareAction,
        getCompareDetailsAction,
        addItemToCompareAction,
        removeItemOnCompareAction,
        removeItemsOnCompareAction,
        assignCompareAction
    } = storeActions;
    const [state, dispatch] = useReducer(wrappedReducer, initialState);

    const removeCompare = useCallback(
        payload => {
            dispatch({ payload, type: 'REMOVE_COMPARE' });
        },
        [dispatch, state]
    );

    const addItemToCompare = useCallback(
        payload => {
            addItemToCompareAction(dispatch, state, payload);
        },
        [dispatch, state]
    );

    const removeItemOnCompare = useCallback(
        payload => {
            removeItemOnCompareAction(dispatch, state, payload);
        },
        [dispatch, state]
    );

    const createCompareList = useCallback(
        payload => {
            createCompareAction(dispatch, state, payload);
        },
        [dispatch, state]
    );

    const getCompareDetails = useCallback(
        payload => {
            getCompareDetailsAction(dispatch, state, payload);
        },
        [dispatch, state]
    );

    const removeItemsOnCompare = useCallback(
        payload => {
            removeItemsOnCompareAction(dispatch, state, payload);
        },
        [dispatch, state]
    );

    const assignCompareList = useCallback(
        payload => {
            assignCompareAction(dispatch, state, payload);
        },
        [dispatch, state]
    );

    const api = useMemo(
        () => ({
            addItemToCompare,
            removeItemsOnCompare,
            removeCompare,
            dispatch,
            removeItemOnCompare,
            createCompareList,
            getCompareDetails,
            assignCompareList
        }),
        [
            addItemToCompare,
            removeCompare,
            dispatch,
            removeItemOnCompare,
            createCompareList,
            getCompareDetails,
            removeItemsOnCompare,
            assignCompareList
        ]
    );

    return [state, api];
};
