module.exports = (targetables, targetablePath) => {
    const productPageQuery = targetables.reactComponent(targetablePath);

    productPageQuery.insertBeforeSource(
        `sku`,
        `price_range {
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
        `
    );
};
