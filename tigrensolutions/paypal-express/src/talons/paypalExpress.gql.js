import { gql } from '@apollo/client';
import { PriceSummaryFragment } from '@magento/peregrine/lib/talons/CartPage/PriceSummary/priceSummaryFragments.gql';
import { AvailablePaymentMethodsFragment } from '@magento/peregrine/lib/talons/CheckoutPage/PaymentInformation/paymentInformation.gql';

export const GET_PAYPAL_EXPRESS_CONFIG_DATA = gql`
    query storeConfigData {
        storeConfig {
            store_code
            paypal_merchant_id
            paypal_sandbox
        }
    }
`;

export const CREATE_PAYPAL_EXPRESS_TOKEN = gql`
    mutation createPaypalExpressToken(
        $cartId: String!
        $urls: PaypalExpressUrlsInput!
    ) {
        createPaypalExpressToken(
            input: { cart_id: $cartId, code: "paypal_express", urls: $urls }
        ) @connection(key: "createPaypalExpressToken") {
            token
            paypal_urls {
                start
                edit
            }
        }
    }
`;

export const SET_PAYPAL_EXPRESS_DETAILS_ON_CART = gql`
    mutation setSelectedPaymentMethod(
        $cartId: String!
        $token: String!
        $payerID: String!
    ) {
        setPaymentMethodOnCart(
            input: {
                cart_id: $cartId
                payment_method: {
                    code: "paypal_express"
                    paypal_express: { payer_id: $payerID, token: $token }
                }
            }
        ) @connection(key: "setPaymentMethodOnCart") {
            cart {
                id
                selected_payment_method {
                    code
                    title
                }
            }
        }
    }
`;

export const SET_BILLING_ADDRESS = gql`
    mutation setBillingAddress($cartId: String!, $sameAsShipping: Boolean!) {
        setBillingAddressOnCart(
            input: {
                cart_id: $cartId
                billing_address: { same_as_shipping: $sameAsShipping }
            }
        ) @connection(key: "setBillingAddressOnCart") {
            cart {
                id
                billing_address {
                    firstname
                    lastname
                    country {
                        code
                    }
                    street
                    city
                    region {
                        code
                    }
                    postcode
                    telephone
                }
                ...PriceSummaryFragment
                ...AvailablePaymentMethodsFragment
            }
        }
    }
    ${PriceSummaryFragment}
    ${AvailablePaymentMethodsFragment}
`;

export default {
    getPaypalExpressConfigQuery: GET_PAYPAL_EXPRESS_CONFIG_DATA,
    createPaypalExpressTokenMutation: CREATE_PAYPAL_EXPRESS_TOKEN,
    setPaypalExpressDetailsOnCartMutation: SET_PAYPAL_EXPRESS_DETAILS_ON_CART,
    setBillingAddressMutation: SET_BILLING_ADDRESS
};
