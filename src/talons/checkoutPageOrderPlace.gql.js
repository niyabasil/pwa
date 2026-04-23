/**
 * Eway_Payment
 *
 */
import {gql} from '@apollo/client';

export const PLACE_ORDER = gql`
    mutation placeOrder($cartId: String!) {
        placeOrder(
            input: { cart_id: $cartId }
        ) @connection(key: "placeOrder") {
            order {
                order_number
              
            }
        }
    }
`;

export const RESTORE_QUOTE = gql`
    mutation restoreQuote($cartId: String!) {
        restoreQuote(input: { cart_id: $cartId })
    }
`;


export default {
    placeEwayOrderMutation: PLACE_ORDER,
    restoreQuoteMutation: RESTORE_QUOTE
};