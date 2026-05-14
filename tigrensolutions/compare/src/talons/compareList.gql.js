import { gql } from '@apollo/client';
import { ProductDetailsCompareFragment } from './productDetailFragment.gql';

const ADD_ITEM_TO_COMPARE = gql`
    mutation addProductToCompareMutation($uid: ID!, $products: [ID!]!) {
        addProductsToCompareList(input: { uid: $uid, products: $products }) {
            uid
            item_count
            attributes {
                code
                label
            }
            items {
                uid
                product {
                    ...ProductDetailsCompareFragment
                }
            }
        }
    }
    ${ProductDetailsCompareFragment}
`;

const CREATE_COMPARE_LIST = gql`
    mutation createCompareList {
        createCompareList {
            uid
        }
    }
`;

const ASSIGN_COMPARE_LIST = gql`
    mutation assignCompareList($uid: ID!) {
        assignCompareListToCustomer(uid: $uid) {
            result
        }
    }
`;

const GET_COMPARE_LIST = gql`
    query getCompareList($uid: ID!) {
        compareList(uid: $uid) {
            uid
            item_count
            attributes {
                code
                label
            }
            items {
                uid
                product {
                    ...ProductDetailsCompareFragment
                }
            }
        }
    }
    ${ProductDetailsCompareFragment}
`;

const REMOVE_COMPARE_ITEM = gql`
    mutation removeProductsFromCompareList($uid: ID!, $products: [ID!]!) {
        removeProductsFromCompareList(
            input: { uid: $uid, products: $products }
        ) {
            uid
            item_count
        }
    }
`;

const GET_STORE_CONFIG_DATA = gql`
    query GetStoreConfigDataForGalleryCE {
        # eslint-disable-next-line @graphql-eslint/require-id-when-available
        storeConfig {
            store_code
            product_url_suffix
            magento_wishlist_general_is_enabled
        }
    }
`;

export const createCompareListMutation = CREATE_COMPARE_LIST;
export const addItemToCompareMutation = ADD_ITEM_TO_COMPARE;
export const getCompareDetailsQuery = GET_COMPARE_LIST;
export const assignCompareListMutation = ASSIGN_COMPARE_LIST;
export const removeItemFromCompareMutation = REMOVE_COMPARE_ITEM;
export const getStoreConfigCompareQuery = GET_STORE_CONFIG_DATA;
