import { gql } from '@apollo/client';

const CHECK_EMAIL = gql`
    query checkEmail($email: String!) {
        isEmailAvailable(email: $email) {
            is_email_available
        }
    }
`;

const SET_GUEST_EMAIL = gql`
    mutation setGuestEmailOnCheckout($cartId: String!, $email: String!) {
        setGuestEmailOnCart(input: { cart_id: $cartId, email: $email })
            @connection(key: "setGuestEmailOnCart") {
            cart {
                id
                email
            }
        }
    }
`;

export default {
    checkEmailQuery: CHECK_EMAIL,
    setGuestEmailMutation: SET_GUEST_EMAIL
};
