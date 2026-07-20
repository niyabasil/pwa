import { gql } from '@apollo/client';
import { CustomerAddressFragment } from './fragments.gql';
import { GET_DEFAULT_SHIPPING } from '@magento/peregrine/lib/talons/CheckoutPage/ShippingInformation/shippingInformation.gql';
import {
    ShippingInformationFragment,
    SET_CUSTOMER_ADDRESS_ON_CART
} from './shippingInformation.gql';

export const GET_CUSTOMER_ADDRESSES = gql`
    query GetCustomerAddresses {
        customer {
            id
            addresses {
                id
                ...CustomerAddressFragment
            }
        }
    }
    ${CustomerAddressFragment}
`;

export const GET_CUSTOMER_CART_ADDRESS = gql`
    query GetCustomerCartAddress {
        customerCart {
            id
            ...ShippingInformationFragment
        }
    }
    ${ShippingInformationFragment}
`;

export default {
    setCustomerAddressOnCartMutation: SET_CUSTOMER_ADDRESS_ON_CART,
    getCustomerAddressesQuery: GET_CUSTOMER_ADDRESSES,
    getCustomerCartAddressQuery: GET_CUSTOMER_CART_ADDRESS,
    getDefaultShippingQuery: GET_DEFAULT_SHIPPING
};
