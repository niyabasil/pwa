import { gql } from '@apollo/client';

export const GET_EWAY_PAYMENT_REQUEST_DATA = gql`
    query getEwayPaymentRequestData($cart_id: String!) {
        ewayPaymentRequestData(cart_id: $cart_id) {
            eway_payment_url
        }
    }
`;

export default {
    getEwayUrl: GET_EWAY_PAYMENT_REQUEST_DATA
};
