module.exports = (targetables, targetablePath) => {
    const searchPageGql = targetables.reactComponent(targetablePath);

    searchPageGql
        .insertAfterSource(
            `price_range {`,
            `
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
                        discount {
                            amount_off
                            percent_off
                            __typename
                        }
                        regular_price {
                            currency
                            value
                        }
                    }`
        )
        .insertAfterSource(
            `maximum_price {`,
            `
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
                         discount {
                            amount_off
                            percent_off
                        }
                        `
        );
};
