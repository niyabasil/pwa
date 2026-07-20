import { gql } from '@apollo/client';
import { DeliveryInformationFragment } from '../Delivery/delivery.gql';
import {
    GiftWrapInformationFragment,
    GiftMessageFragment
} from '../AdditionalOptions/GiftWrap/giftWrap.gql';
import { SuccessPageFragment } from '../SuccessPage/successPageFragment.gql';
import { AddressFragment } from '../ShippingInformation/fragments.gql';

export const GET_CHECKOUT_DETAILS = gql`
    query getCheckoutDetails($cartId: String!) {
        cart(cart_id: $cartId) {
            id
            items {
                id
                uid
                item_gift_message {
                    ...GiftMessageFragment
                }
                product {
                    id
                    uid
                    name
                    stock_status
                }
            }
            # If total quantity is falsy we render empty.
            total_quantity
            available_payment_methods {
                code
                title
            }
            selected_payment_method {
                code
                title
            }
            email
            shipping_addresses {
                ...AddressFragment
            }
            order_gift_message {
                ...GiftMessageFragment
            }

            ...GiftWrapInformationFragment
            ...DeliveryInformationFragment
        }
    }
    ${AddressFragment}
    ${DeliveryInformationFragment}
    ${GiftWrapInformationFragment}
    ${GiftMessageFragment}
`;

export const GET_ORDER_DETAILS = gql`
    query getOrderDetails($cartId: String!) {
        getAdditionalFields(cartId: $cartId) {
            is_register
            is_subscribe
            comment
        }
        cart(cart_id: $cartId) {
            id
            ...SuccessPageFragment
            ...GiftWrapInformationFragment
            ...DeliveryInformationFragment
        }
    }
    ${SuccessPageFragment}
    ${DeliveryInformationFragment}
    ${GiftWrapInformationFragment}
`;

export const PLACE_ORDER = gql`
    mutation placeOrder($cartId: String!) {
        placeOrder(input: { cart_id: $cartId }) @connection(key: "placeOrder") {
            order {
                order_number
                amasty_order_date
            }
        }
    }
`;

export const CREATE_CART = gql`
    # This mutation will return a masked cart id. If a bearer token is provided for
    # a logged in user it will return the cart id for that user.
    mutation createCart {
        cartId: createEmptyCart
    }
`;

export default {
    getCheckoutDetailsQuery: GET_CHECKOUT_DETAILS,
    getOrderDetailsQuery: GET_ORDER_DETAILS,
    placeOrderMutation: PLACE_ORDER,
    createCartMutation: CREATE_CART
};
