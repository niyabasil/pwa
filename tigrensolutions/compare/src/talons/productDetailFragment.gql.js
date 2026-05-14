import { gql } from '@apollo/client';

export const ProductDetailsCompareFragment = gql`
    fragment ProductDetailsCompareFragment on ProductInterface {
        __typename
        rating_summary
        review_count
        categories {
            id
            uid
            breadcrumbs {
                category_uid
                __typename
            }
            __typename
        }
        description {
            html
            __typename
        }
        short_description {
            html
            __typename
        }
        id
        uid
        type_id
        attributes {
            label
            value
            code
            __typename
        }
        media_gallery_entries {
            id
            uid
            label
            position
            disabled
            file
            __typename
        }
        meta_description
        name
        price {
            regularPrice {
                amount {
                    currency
                    value
                    __typename
                }
                __typename
            }
            __typename
        }
        price_range {
            maximum_price {
                maximum_final_price_excl_tax {
                    currency
                    value
                }
                final_price {
                    currency
                    value
                }
                fixed_product_taxes {
                    amount {
                        currency
                        value
                    }
                    label
                }
                regular_price {
                    currency
                    value
                    __typename
                }
                discount {
                    amount_off
                    percent_off
                }
            }
            minimum_price {
                minimum_final_price_excl_tax {
                    currency
                    value
                }
                final_price {
                    currency
                    value
                }
                fixed_product_taxes {
                    amount {
                        currency
                        value
                    }
                    label
                }
                regular_price {
                    currency
                    value
                }
                discount {
                    amount_off
                    percent_off
                    __typename
                }
            }
        }
        sku
        small_image {
            url
            __typename
        }
        stock_status
        url_key
        url_rewrites {
            url
        }
        ... on ConfigurableProduct {
            configurable_options {
                attribute_code
                attribute_id
                uid
                label
                values {
                    uid
                    default_label
                    label
                    store_label
                    use_default_value
                    value_index
                    swatch_data {
                        ... on ImageSwatchData {
                            thumbnail
                            __typename
                        }
                        value
                        __typename
                    }
                    __typename
                }
                __typename
            }
            variants {
                attributes {
                    code
                    value_index
                    __typename
                }

                product {
                    uid
                    media_gallery_entries {
                        id
                        uid
                        disabled
                        file
                        label
                        position
                        __typename
                    }
                    sku
                    stock_status
                    price_range {
                        maximum_price {
                            maximum_final_price_excl_tax {
                                currency
                                value
                            }
                            final_price {
                                currency
                                value
                            }
                            fixed_product_taxes {
                                amount {
                                    currency
                                    value
                                }
                                label
                            }
                            regular_price {
                                currency
                                value
                                __typename
                            }
                            discount {
                                amount_off
                                percent_off
                            }
                        }
                        minimum_price {
                            minimum_final_price_excl_tax {
                                currency
                                value
                            }
                            final_price {
                                currency
                                value
                            }
                            fixed_product_taxes {
                                amount {
                                    currency
                                    value
                                }
                                label
                            }
                            regular_price {
                                currency
                                value
                            }
                            discount {
                                amount_off
                                percent_off
                                __typename
                            }
                        }
                    }
                    price {
                        regularPrice {
                            amount {
                                currency
                                value
                                __typename
                            }
                            __typename
                        }
                        __typename
                    }
                    __typename
                }
                __typename
            }
            __typename
        }
    }
`;
