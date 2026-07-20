import { gql } from '@apollo/client';

export const DeliveryInformationFragment = gql`
    fragment DeliveryInformationFragment on Cart {
        amasty_delivery_date {
            date
            time {
                value
                displayValue
            }
            comment
        }
    }
`;

const UPDATE_DELIVERY_INFORMATION = gql`
    mutation updateDeliveryInformation(
        $cartId: String!
        $date: String!
        $time: Int
        $comment: String
    ) {
        updateDeliveryInformation(
            input: {
                cart_id: $cartId
                date: $date
                time: $time
                comment: $comment
            }
        ) @connection(key: "updateDeliveryInformation") {
            cart {
                id
                ...DeliveryInformationFragment
            }
        }
    }
    ${DeliveryInformationFragment}
`;

export default {
    updateDeliveryInformationMutation: UPDATE_DELIVERY_INFORMATION
};
