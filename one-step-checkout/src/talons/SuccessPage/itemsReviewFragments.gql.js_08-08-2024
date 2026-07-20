import { gql } from '@apollo/client';
import { GiftMessageFragment } from '../AdditionalOptions/GiftWrap/giftWrap.gql';

export const ItemsReviewFragment = gql`
    fragment ItemsReviewFragment on Cart {
        id
        total_quantity
        items {
            id
            product {
                id
                name
                thumbnail {
                    url
                }
                ... on ConfigurableProduct {
                    variants {
                        attributes {
                            uid
                        }
                        product {
                            id
                            thumbnail {
                                url
                            }
                        }
                    }
                }
            }
            quantity
            item_gift_message {
                ...GiftMessageFragment
            }
            ... on ConfigurableCartItem {
                configurable_options {
                    id
                    option_label
                    value_id
                    value_label
                }
            }
        }
    }
    ${GiftMessageFragment}
`;
