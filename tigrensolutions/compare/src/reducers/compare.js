import BrowserPersistence from '@magento/peregrine/lib/util/simplePersistence';

const storage = new BrowserPersistence();

const initialState = {
    compareId: storage.getItem('compareId') || null,
    compareItems: [],
    error: null,
    isLoading: false,
    isDeleting: false,
    isCreating: false,
    isShowStickyButton: false
};

export const reducer = (state, action) => {
    const { payload, type, isError, maxItemsCompare } = action;

    switch (type) {
        case 'ADD_ITEM_REQUEST': {
            return {
                ...state,
                isLoading: true
            };
        }
        case 'ADD_ITEM_RECEIVE': {
            if (isError) {
                return {
                    ...state,
                    addItemError: payload,
                    isLoading: false
                };
            }

            return {
                ...state,
                compareItems: payload.splice(0, maxItemsCompare),
                addItemError: null,
                isLoading: false
            };
        }
        case 'REMOVE_ITEM_REQUEST': {
            return {
                ...state,
                isDeleting: true
            };
        }
        case 'REMOVE_ITEM_RECEIVE': {
            if (isError) {
                return {
                    ...state,
                    addItemError: payload,
                    isDeleting: false
                };
            }

            const compareItems = ((state && state.compareItems) || []).filter(
                item => {
                    const productId = item && item.product && item.product.id;
                    return productId !== payload;
                }
            );

            return {
                ...state,
                compareItems: compareItems,
                isShowStickyButton: true,
                isLoading: false
            };
        }
        case 'GET_DETAILS_REQUEST': {
            return {
                ...state,
                isLoading: true,
                isCreating: true
            };
        }
        case 'GET_DETAILS_RECEIVE': {
            if (isError) {
                return {
                    ...state,
                    getDetailsError: payload,
                    isLoading: false,
                    isCreating: false
                };
            }
            return {
                ...state,
                compareItems: payload.splice(0, maxItemsCompare),
                isShowStickyButton: true,
                isLoading: false,
                isCreating: false
            };
        }
        case 'CREATE_COMPARE_REQUEST': {
            return {
                ...state,
                isLoading: true,
                isCreating: true
            };
        }
        case 'CREATE_COMPARE_RECEIVE': {
            if (isError) {
                return {
                    ...state,
                    createCompareError: payload,
                    isLoading: false,
                    isCreating: false
                };
            }
            storage.setItem('compareId', payload);
            return {
                ...state,
                isLoading: false,
                isCreating: false,
                compareId: payload
            };
        }
        case 'ASSIGN_COMPARE_REQUEST': {
            return {
                ...state,
                isLoading: true,
                isCreating: true
            };
        }
        case 'ASSIGN_COMPARE_RECEIVE': {
            if (isError) {
                return {
                    ...state,
                    error: payload,
                    isLoading: false,
                    isCreating: false
                };
            }

            return {
                ...state,
                isLoading: false,
                isCreating: false
            };
        }
        case 'REMOVE_COMPARE': {
            storage.removeItem('compareId');
            return { ...initialState, compareId: null };
        }
        case 'REMOVE_ALL_ITEMS_REQUEST': {
            return {
                ...state,
                isDeleting: true
            };
        }
        case 'REMOVE_ALL_ITEMS_RECEIVE': {
            if (isError) {
                return {
                    ...state,
                    addItemError: payload,
                    isDeleting: false
                };
            }

            return {
                ...state,
                isDeleting: false,
                compareItems: [],
                isShowStickyButton: true,
                isLoading: false
            };
        }
        default: {
            return state;
        }
    }
};

export default {
    reducer: reducer,
    initialState: initialState
};
