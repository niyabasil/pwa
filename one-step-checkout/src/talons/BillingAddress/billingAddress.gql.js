import { gql } from '@apollo/client';
import { AddressFragment } from '../ShippingInformation/fragments.gql';
import { PriceSummaryFragment } from '@magento/peregrine/lib/talons/CartPage/PriceSummary/priceSummaryFragments.gql';
import { AvailablePaymentMethodsFragment } from '@magento/peregrine/lib/talons/CheckoutPage/PaymentInformation/paymentInformation.gql';

export const GET_IS_BILLING_ADDRESS_SAME = gql`
    query getIsBillingAddressSame($cartId: String!) {
        cart(cart_id: $cartId) @client {
            id
            isBillingAddressSame
        }
    }
`;

export const GET_BILLING_ADDRESS = gql`
    query getBillingAddress($cartId: String!) {
        cart(cart_id: $cartId) {
            id
            billingAddress: billing_address {
                ...AddressFragment
            }
        }
    }
    ${AddressFragment}
`;

export const GET_SHIPPING_ADDRESS = gql`
    query getSelectedShippingAddress($cartId: String!) {
        cart(cart_id: $cartId) {
            id
            shippingAddresses: shipping_addresses {
                ...AddressFragment
            }
        }
    }
    ${AddressFragment}
`;

export const SET_BILLING_ADDRESS = gql`
    mutation setBillingAddress($cartId: String!, $address: CartAddressInput!) {
        setBillingAddressOnCart(
            input: { cart_id: $cartId, billing_address: { address: $address } }
        ) @connection(key: "setBillingAddressOnCart") {
            cart {
                id
                billing_address {
                    ...AddressFragment
                }
                ...PriceSummaryFragment
                ...AvailablePaymentMethodsFragment
            }
        }
    }
    ${PriceSummaryFragment}
    ${AvailablePaymentMethodsFragment}
    ${AddressFragment}
`;

export default {
    getBillingAddressQuery: GET_BILLING_ADDRESS,
    getIsBillingAddressSameQuery: GET_IS_BILLING_ADDRESS_SAME,
    getShippingAddressQuery: GET_SHIPPING_ADDRESS,
    setBillingAddressMutation: SET_BILLING_ADDRESS
};
