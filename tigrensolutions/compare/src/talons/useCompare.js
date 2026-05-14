import React, { useCallback, useEffect } from 'react';

import { useMutation, useQuery } from '@apollo/client';

import { useIntl } from 'react-intl';
import { useToasts } from '@magento/peregrine';
import BrowserPersistence from '@magento/peregrine/lib/util/simplePersistence';

import { useCompareState } from './useCompareState';

import {
    addItemToCompareMutation,
    createCompareListMutation,
    getCompareDetailsQuery,
    removeItemFromCompareMutation,
    getStoreConfigCompareQuery,
    assignCompareListMutation
} from './compareList.gql';

import { useAwaitQuery } from '@magento/peregrine/lib/hooks/useAwaitQuery';

const storage = new BrowserPersistence();

export const useCompare = () => {
    // eslint-disable-next-line react-hooks/rules-of-hooks
    const { formatMessage } = useIntl();

    // eslint-disable-next-line react-hooks/rules-of-hooks
    const [, { addToast }] = useToasts();

    const [compareState, compareApi] = useCompareState();

    const { isLoading, compareItems, compareId } = compareState;
    const {
        addItemToCompare,
        removeItemOnCompare,
        removeItemsOnCompare,
        getCompareDetails,
        createCompareList,
        assignCompareList: assignCompareListAction
    } = compareApi;

    const fetchCompareDetails = useAwaitQuery(getCompareDetailsQuery);
    const [createCompare] = useMutation(createCompareListMutation);
    const [assignCompareList] = useMutation(assignCompareListMutation);
    const [addItem, { loading: isAdding }] = useMutation(
        addItemToCompareMutation
    );
    const [removeItem, { loading: isRemoving }] = useMutation(
        removeItemFromCompareMutation
    );

    const handleAddToCompare = useCallback(
        async ({ product }) => {
            addItemToCompare({
                formatMessage,
                addItem,
                product,
                addToast
            });
        },
        [compareId, compareItems]
    );

    const handleRemoveProduct = useCallback(
        product => {
            removeItemOnCompare({
                removeItem,
                product,
                formatMessage,
                addToast
            });
        },
        [compareId, compareItems]
    );

    const handleRemoveAllProduct = useCallback(
        listId => {
            removeItemsOnCompare({
                removeItem,
                listId,
                formatMessage,
                addToast
            });
        },
        [compareId, compareItems]
    );

    const assignCompare = useCallback(() => {
        assignCompareListAction({
            assignCompareList,
            formatMessage,
            addToast
        });
    }, [assignCompareList, addToast, formatMessage]);

    const { data: storeConfigData } = useQuery(getStoreConfigCompareQuery, {
        fetchPolicy: 'cache-and-network'
    });
    const storeConfig = storeConfigData ? storeConfigData.storeConfig : null;
    const productUrlSuffix = storeConfig && storeConfig.product_url_suffix;

    useEffect(() => {
        if (!storage.getItem('compareId')) {
            createCompareList({
                createCompare
            });
        }
    }, [compareId]);

    useEffect(() => {
        if (compareId) {
            getCompareDetails({
                fetchCompareDetails
            });
        }
    }, [compareId]);

    return [
        {
            ...compareState,
            hasError: false,
            showLoading: isLoading,
            isRemoving,
            isAdding,
            handleAddToCompare,
            handleRemoveProduct,
            handleRemoveAllProduct,
            productUrlSuffix
        },
        { ...compareApi, assignCompare }
    ];
};
