import { gql } from '@apollo/client';
import { GET_DEFAULT_SHIPPING } from '@magento/peregrine/lib/talons/CheckoutPage/ShippingInformation/shippingInformation.gql';
import { AddressFragment } from './fragments.gql';
import { ShippingMethodsCheckoutFragment } from '@magento/peregrine/lib/talons/CheckoutPage/ShippingMethod/shippingMethodFragments.gql';
import { PriceSummaryFragment } from '@magento/peregrine/lib/talons/CartPage/PriceSummary/priceSummaryFragments.gql';
import { AvailablePaymentMethodsFragment } from '@magento/peregrine/lib/talons/CheckoutPage/PaymentInformation/paymentInformation.gql';

export const ShippingInformationFragment = gql`
    fragment ShippingInformationFragment on Cart {
        id
        email
        shipping_addresses {
            ...AddressFragment
        }
    }
    ${AddressFragment}
`;

export const GET_SHIPPING_INFORMATION = gql`
    query GetShippingInformation($cartId: String!) {
        cart(cart_id: $cartId) {
            id
            ...ShippingInformationFragment
        }
    }
    ${ShippingInformationFragment}
`;

export const SET_SHIPPING_ADDRESS_MUTATION = gql`
    mutation SetShippingAddress($cartId: String!, $address: CartAddressInput!) {
        setShippingAddressesOnCart(
            input: {
                cart_id: $cartId
                shipping_addresses: [{ address: $address }]
            }
        ) @connection(key: "setShippingAddressesOnCart") {
            cart {
                id
                ...ShippingInformationFragment
                ...ShippingMethodsCheckoutFragment
                ...PriceSummaryFragment
                ...AvailablePaymentMethodsFragment
            }
        }
    }
    ${ShippingInformationFragment}
    ${ShippingMethodsCheckoutFragment}
    ${PriceSummaryFragment}
    ${AvailablePaymentMethodsFragment}
`;

export const SET_CUSTOMER_ADDRESS_ON_CART = gql`
    mutation SetCustomerAddressOnCart($cartId: String!, $addressId: Int!) {
        setShippingAddressesOnCart(
            input: {
                cart_id: $cartId
                shipping_addresses: [{ customer_address_id: $addressId }]
            }
        ) @connection(key: "setShippingAddressesOnCart") {
            cart {
                id
                ...ShippingInformationFragment
                ...ShippingMethodsCheckoutFragment
                ...PriceSummaryFragment
                ...AvailablePaymentMethodsFragment
            }
        }
    }
    ${ShippingInformationFragment}
    ${ShippingMethodsCheckoutFragment}
    ${PriceSummaryFragment}
    ${AvailablePaymentMethodsFragment}
`;

export default {
    setShippingAddressMutation: SET_SHIPPING_ADDRESS_MUTATION,
    getDefaultShippingQuery: GET_DEFAULT_SHIPPING,
    setDefaultAddressOnCartMutation: SET_CUSTOMER_ADDRESS_ON_CART
};
