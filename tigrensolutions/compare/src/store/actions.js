import React from 'react';
import get from 'lodash.get';
import { BrowserPersistence } from '@magento/peregrine/lib/util';

const storage = new BrowserPersistence();

const MAX_ITEMS_COMPARE = 4;

const TIME_TOAST = 3000;

const createCompareAction = async (dispatch, state, payload) => {
    const { createCompare } = payload;

    const { compareId } = state;

    if (compareId) {
        return;
    }

    dispatch({ payload, type: 'CREATE_COMPARE_REQUEST' });
    try {
        const { data } = await createCompare();
        const { uid } = data && data.createCompareList;

        dispatch({ payload: uid, type: 'CREATE_COMPARE_RECEIVE' });
    } catch (error) {
        dispatch({
            payload: error,
            type: 'CREATE_COMPARE_RECEIVE',
            isError: true
        });
    }
};

const getCompareDetailsAction = async (dispatch, state, payload) => {
    const { fetchCompareDetails } = payload;
    const { compareId } = state;

    // First, create compare id
    if (!compareId) {
        return;
    }

    dispatch({ payload, type: 'GET_DETAILS_REQUEST' });
    try {
        const { data } = await fetchCompareDetails({
            variables: {
                uid: compareId
            },
            fetchPolicy: 'no-cache',
            nextFetchPolicy: 'no-cache'
        });
        if (get(data, 'compareList', [])) {
            const compareItems = get(data, 'compareList.items', []);
            dispatch({
                payload: compareItems,
                type: 'GET_DETAILS_RECEIVE',
                maxItemsCompare: MAX_ITEMS_COMPARE
            });
        } else {
            dispatch({ payload, type: 'REMOVE_COMPARE' });
        }
    } catch (error) {
        dispatch({ payload, type: 'REMOVE_COMPARE' });
    }
};

const assignCompareAction = async (dispatch, state, payload) => {
    const { assignCompareList, formatMessage, addToast } = payload;
    const { compareId: compareIdState } = state;
    const errorMessage = "Can't assign compare list";
    const compareId = compareIdState || storage.getItem('compareId');

    if (!compareId) {
        addToast({
            type: 'error',
            message: formatMessage({
                id: 'compare.noCompareIdError',
                defaultMessage: errorMessage
            }),
            timeout: TIME_TOAST
        });
        dispatch({
            type: 'ASSIGN_COMPARE_RECEIVE',
            isError: true
        });
    } else {
        dispatch({ payload, type: 'ASSIGN_COMPARE_REQUEST' });
        try {
            const { data } = await assignCompareList({
                variables: {
                    uid: compareId
                },
                fetchPolicy: 'no-cache',
                nextFetchPolicy: 'no-cache'
            });

            const result = get(data, 'assignCompareListToCustomer.result', []);

            if (result) {
                dispatch({
                    type: 'ASSIGN_COMPARE_RECEIVE'
                });
            } else {
                addToast({
                    type: 'error',
                    message: formatMessage({
                        id: 'compare.noCompareIdError',
                        defaultMessage: errorMessage
                    }),
                    timeout: TIME_TOAST
                });
                dispatch({
                    payload: errorMessage,
                    type: 'ASSIGN_COMPARE_RECEIVE',
                    isError: true
                });
            }
        } catch (error) {
            if (process.env.NODE_ENV === 'development') {
                console.error(error);
            }
            addToast({
                type: 'error',
                message: formatMessage({
                    id: 'compare.noCompareIdError',
                    defaultMessage: error.message
                }),
                timeout: TIME_TOAST
            });
            dispatch({
                payload: error,
                type: 'ASSIGN_COMPARE_RECEIVE',
                isError: true
            });
        }
    }
};

const addItemToCompareAction = async (dispatch, state, payload) => {
    const { addItem, product, formatMessage, addToast } = payload;

    const { compareId, compareItems } = state;

    let errorMessage;

    if (!compareId) {
        errorMessage = formatMessage({
            id: 'compare.noCompareIdError',
            defaultMessage: "Can't get compare list"
        });
    }

    if (!product) {
        errorMessage = formatMessage({
            id: 'compare.noCompareIdError',
            defaultMessage: "Can't get compare list"
        });
    }

    if (compareItems && compareItems.length === MAX_ITEMS_COMPARE) {
        errorMessage = formatMessage(
            {
                id: 'compare.onlyAddThreeItems',
                defaultMessage:
                    'You have already added {maxItems} items into the comparison list.'
            },
            {
                maxItems: MAX_ITEMS_COMPARE
            }
        );
    }

    if (errorMessage) {
        addToast({
            type: 'error',
            message: errorMessage,
            timeout: TIME_TOAST
        });
        dispatch({
            payload: new Error(errorMessage),
            type: 'ADD_ITEM_RECEIVE',
            isError: true
        });
    } else {
        try {
            const { data } = await addItem({
                variables: {
                    uid: compareId,
                    products: [product.id]
                }
            });
            const compareItems = get(
                data,
                'addProductsToCompareList.items',
                []
            );
            dispatch({
                payload: compareItems.splice(0, MAX_ITEMS_COMPARE),
                type: 'ADD_ITEM_RECEIVE',
                maxItemsCompare: MAX_ITEMS_COMPARE
            });
            addToast({
                type: 'info',
                message: formatMessage(
                    {
                        id: 'addCompare.success',
                        defaultMessage:
                            'You added product <strong>{name}</strong> to the comparison list.'
                    },
                    {
                        name: product.name,
                        strong: chunks => <strong>{chunks}</strong>
                    }
                ),
                timeout: TIME_TOAST
            });
        } catch (error) {
            addToast({
                type: 'error',
                message: formatMessage({
                    id: 'addCompare.error',
                    defaultMessage: error.message
                }),
                timeout: TIME_TOAST
            });
            dispatch({
                payload: error.message,
                type: 'ADD_ITEM_RECEIVE',
                isError: true
            });
        }
    }
};

const removeItemOnCompareAction = async (dispatch, state, payload) => {
    const { removeItem, product, formatMessage, addToast } = payload;
    const { compareId } = state;

    // First, create compare id
    if (!compareId) return;

    dispatch({ type: 'REMOVE_ITEM_REQUEST' });
    try {
        await removeItem({
            variables: {
                uid: compareId,
                products: [product.id]
            },
            fetchPolicy: 'no-cache',
            nextFetchPolicy: 'no-cache'
        });
        dispatch({ payload: product.id, type: 'REMOVE_ITEM_RECEIVE' });
        addToast({
            type: 'info',
            message: formatMessage(
                {
                    id: 'addCompare.removeSuccess',
                    defaultMessage:
                        'You removed product <strong>{name}</strong> from the comparison list.'
                },
                {
                    name: product.name,
                    strong: chunks => <strong>{chunks}</strong>
                }
            ),
            timeout: TIME_TOAST
        });
    } catch (error) {
        addToast({
            type: 'error',
            message: formatMessage({
                id: 'addCompare.error',
                defaultMessage: error.message
            }),
            timeout: TIME_TOAST
        });
        dispatch({
            payload: error,
            type: 'REMOVE_ITEM_RECEIVE',
            isError: true
        });
    }
};

const removeItemsOnCompareAction = async (dispatch, state, payload) => {
    const { removeItem, listId, formatMessage, addToast } = payload;
    const { compareId } = state;

    // First, create compare id
    if (!compareId) return;

    dispatch({ type: 'REMOVE_ALL_ITEMS_REQUEST' });
    try {
        await removeItem({
            variables: {
                uid: compareId,
                products: listId,
                typePolicies: 'network-only'
            }
        });
        dispatch({ type: 'REMOVE_ALL_ITEMS_RECEIVE' });
        addToast({
            type: 'info',
            message: formatMessage({
                id: 'addCompare.empty',
                defaultMessage: `You have no items to compare.`
            }),
            timeout: TIME_TOAST
        });
    } catch (error) {
        addToast({
            type: 'error',
            message: formatMessage({
                id: 'addCompare.error',
                defaultMessage: error.message
            }),
            timeout: TIME_TOAST
        });
        dispatch({
            payload: error,
            type: 'REMOVE_ALL_ITEMS_RECEIVE',
            isError: true
        });
    }
};

export default {
    createCompareAction: createCompareAction,
    getCompareDetailsAction: getCompareDetailsAction,
    addItemToCompareAction: addItemToCompareAction,
    removeItemOnCompareAction: removeItemOnCompareAction,
    removeItemsOnCompareAction: removeItemsOnCompareAction,
    assignCompareAction: assignCompareAction
};
