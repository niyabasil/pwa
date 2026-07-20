import { gql } from '@apollo/client';

export const AddressFragment = gql`
    fragment AddressFragment on CartAddressInterface {
        firstname
        lastname
        street
        city
        region {
            region_id
            code
            label
        }
        postcode
        country {
            code
            label
        }
        telephone
        custom_attributes {
            attribute_code
            value
        }
    }
`;

export const CustomerAddressFragment = gql`
    fragment CustomerAddressFragment on CustomerAddress {
        id
        city
        country_code
        default_shipping
        firstname
        lastname
        postcode
        region {
            region
            region_code
            region_id
        }
        street
        telephone
        custom_attributes {
            attribute_code
            value
        }
    }
`;
