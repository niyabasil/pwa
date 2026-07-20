import { gql } from '@apollo/client';
import { PriceSummaryFragment } from '@magento/peregrine/lib/talons/CartPage/PriceSummary/priceSummaryFragments.gql';

export const GiftWrapInformationFragment = gql`
    fragment GiftWrapInformationFragment on Cart {
        amasty_gift_wrap {
            amount
            base_amount
            currency_code
        }
    }
`;

export const GiftMessageFragment = gql`
    fragment GiftMessageFragment on GiftMessageForOrderOutput {
        message
        sender
        recipient
    }
`;

const UPDATE_GIFT_WRAP_INFORMATION = gql`
    mutation updateGiftWrapInformation($cartId: String!, $checked: Boolean!) {
        updateGiftWrapInformation(
            input: { cart_id: $cartId, checked: $checked }
        ) @connection(key: "updateGiftWrapInformation") {
            cart {
                id
                ...PriceSummaryFragment
                ...GiftWrapInformationFragment
            }
        }
    }
    ${PriceSummaryFragment}
    ${GiftWrapInformationFragment}
`;

const ADD_GIFT_MESSAGE_FOR_ORDER = gql`
    mutation addGiftMessageForWholeOrder(
        $messageInput: AddGiftMessageForWholeOrderInput
    ) {
        addGiftMessageForWholeOrder(input: $messageInput)
            @connection(key: "addGiftMessageForWholeOrder") {
            response
            cart {
                id
                order_gift_message {
                    ...GiftMessageFragment
                }
            }
        }
    }
    ${GiftMessageFragment}
`;

const ADD_GIFT_MESSAGE_FOR_ITEMS = gql`
    mutation addGiftMessageForOrderItems(
        $cartId: String!
        $items: [GiftMessageForOrderItemsInput!]!
    ) {
        addGiftMessageForOrderItems(
            input: { cart_id: $cartId, cart_items: $items }
        ) @connection(key: "addGiftMessageForOrderItems") {
            response
            cart {
                id
                items {
                    uid
                    item_gift_message {
                        ...GiftMessageFragment
                    }
                }
            }
        }
    }
    ${GiftMessageFragment}
`;

export default {
    updateGiftWrapInformationMutation: UPDATE_GIFT_WRAP_INFORMATION,
    addGiftMessageForWholeOrderMutation: ADD_GIFT_MESSAGE_FOR_ORDER,
    addGiftMessageForItemsMutation: ADD_GIFT_MESSAGE_FOR_ITEMS
};
