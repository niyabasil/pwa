/**
 * Copyright © 2024 , Inc. All rights reserved.
 * See DISCLAIMER.md for disclaimer details.
 */
import {gql} from '@apollo/client';
import {OrderConfirmationPageFragment} from '@magento/peregrine/lib/talons/CheckoutPage/OrderConfirmationPage/orderConfirmationPageFragments.gql';


export const GET_ORDER_DETAILS = gql`
    query getOrderDetails($cartId: String!) {
        ewayCart(cart_id: $cartId) {
            id
            selected_payment_method {
                purchase_order_number
                title
                code
            }
            ...OrderConfirmationPageFragment
        }
    }
    ${OrderConfirmationPageFragment}
`;


export default {
    getOrderDetailsQuery: GET_ORDER_DETAILS
};