import { useMemo } from 'react';
import { Util } from '@magento/peregrine';

import { findMatchingVariant } from '@tigrensolutions/base/src/util/findMatchingProductVariant';
import { isProductConfigurable } from '@tigrensolutions/base/src/util/isProductConfigurable';
import { useApolloClient } from '@apollo/client';

import { GET_STORE_CONFIG_DATA } from '@magento/peregrine/lib/talons/Header/storeSwitcher.gql.js';

const { BrowserPersistence } = Util;
const storage = new BrowserPersistence();

const INITIAL_OPTION_CODES = new Map();
const INITIAL_OPTION_SELECTIONS = new Map();

const deriveOptionCodesFromProduct = product => {
    // If this is a simple product it has no option codes.
    if (!isProductConfigurable(product)) {
        return INITIAL_OPTION_CODES;
    }

    // Initialize optionCodes based on the options of the product.

    const initialOptionCodes = new Map();

    if (product.configurable_options) {
        for (const {
            attribute_id,
            attribute_code
        } of product.configurable_options) {
            initialOptionCodes.set(attribute_id, attribute_code);
        }
    }

    return initialOptionCodes;
};

// Similar to deriving the initial codes for each option.
const deriveOptionSelectionsFromProduct = product => {
    if (!isProductConfigurable(product)) {
        return INITIAL_OPTION_SELECTIONS;
    }

    const initialOptionSelections = new Map();
    if (product.configurable_options) {
        for (const { attribute_id, values } of product.configurable_options) {
            initialOptionSelections.set(
                attribute_id,
                values[0] ? values[0].value_index : undefined
            );
        }
    }

    return initialOptionSelections;
};

const getTierPrice = product => {
    if (product?.price_tiers) {
        return product.price_tiers;
    }
};

//Sort products with increasing quantity
const tierPriceQuantitySort = product => {
    if (product) {
        const listTierPrice = getTierPrice(product);
        if (listTierPrice?.length > 0) {
            const sortProductQty = [...listTierPrice].sort((index, next) => {
                return index.quantity - next.quantity;
            });
            return sortProductQty;
        }
    }
};

//Get the product with the nearest smaller quantity
const getNearestSmallQty = (product, quantity) => {
    let result = product.filter(item => item.quantity < quantity);
    return result[result.length - 1];
};

const filterQuantity = (product, quantity) => {
    if (product) {
        const listTierPrice = getTierPrice(product);
        if (listTierPrice?.length > 0) {
            let tierProduct = listTierPrice.filter(
                item => item.quantity === quantity
            );
            if (tierProduct.length >= 1) {
                return tierProduct[0];
            } else {
                let sortProductQtyAsc = tierPriceQuantitySort(product);
                let sortProductQtyLength = sortProductQtyAsc.length;
                if (
                    quantity >
                    sortProductQtyAsc[sortProductQtyLength - 1].quantity
                ) {
                    return sortProductQtyAsc[sortProductQtyLength - 1];
                } else if (quantity < sortProductQtyAsc[0].quantity) {
                    return;
                } else {
                    return getNearestSmallQty(sortProductQtyAsc, quantity);
                }
            }
        }
    }
};

const getConfigPrice = (
    product,
    optionCodes = [],
    optionSelections,
    quantity
) => {
    let price;
    const isConfigurable = isProductConfigurable(product);

    let optionsSelected = null;
    if (optionSelections) {
        optionsSelected =
            Array.from(optionSelections.values()).filter(value => !!value)
                .length > 0;
    }

    if (!isConfigurable) {
        price = product.price_range || product.price;
    } else {
        const { variants } = product;
        let item;
        if (optionsSelected) {
            item = findMatchingVariant({
                optionCodes,
                optionSelections,
                variants
            });
        } else {
            item = findMatchingVariant({
                variants,
                optionCodes: deriveOptionCodesFromProduct(product) || [],
                optionSelections: deriveOptionSelectionsFromProduct(product)
            });
        }

        if (item && item.product && item.product.price_range) {
            price = item.product.price_range;
        } else {
            price = product.price_range;
        }
    }

    const configPrice = { ...price };

    return configPrice;
};

const getCustomizeOptionPrice = (price, type, product) => {
    if (type === 'FIXED') {
        return price;
    } else if (type === 'PERCENT') {
        const { base_price_excl_tax, base_price } = product;

        const final = base_price_excl_tax ? base_price_excl_tax : base_price;

        const showPrice = (price * final) / 100;

        return showPrice;
    }
};

const getOptionPrice = (product, customizeOptions = []) => {
    if (!Array.isArray(product.options))
        return { optionPrice: 0, optionPriceExclTax: 0 };

    let optionPrice = 0;
    let optionPriceExclTax = 0;

    for (const [id, value] of customizeOptions) {
        if (!value) continue;
        const optionSelected = product.options.find(
            option => option.option_id == id
        );
        if (!optionSelected) continue;
        if (optionSelected.value) {
            if (Array.isArray(optionSelected.value)) {
                if (Array.isArray(value)) {
                    optionSelected.value.forEach(optionValue => {
                        value.forEach(valueSelected => {
                            if (optionValue.option_type_id == valueSelected) {
                                if (!!optionValue.final_price) {
                                    optionPrice += getCustomizeOptionPrice(
                                        optionValue.final_price,
                                        optionValue.price_type,
                                        product
                                    );
                                    optionPriceExclTax += getCustomizeOptionPrice(
                                        optionValue.price,
                                        optionValue.price_type,
                                        product
                                    );
                                } else {
                                    optionPrice += getCustomizeOptionPrice(
                                        optionValue.price,
                                        optionValue.price_type,
                                        product
                                    );
                                    optionPriceExclTax = optionPrice;
                                }
                            }
                        });
                    });
                } else {
                    optionSelected.value.forEach(optionValue => {
                        if (optionValue.option_type_id == value) {
                            if (!!optionValue.final_price) {
                                optionPrice += getCustomizeOptionPrice(
                                    optionValue.final_price,
                                    optionValue.price_type,
                                    product
                                );
                                optionPriceExclTax += getCustomizeOptionPrice(
                                    optionValue.price,
                                    optionValue.price_type,
                                    product
                                );
                            } else {
                                optionPrice += getCustomizeOptionPrice(
                                    optionValue.price,
                                    optionValue.price_type,
                                    product
                                );
                                optionPriceExclTax = optionPrice;
                            }
                        }
                    });
                }
            } else {
                if (!!optionSelected.value.final_price) {
                    optionPrice += getCustomizeOptionPrice(
                        optionSelected.value.final_price,
                        optionSelected.value.price_type,
                        product
                    );
                    optionPriceExclTax += getCustomizeOptionPrice(
                        optionSelected.value.price,
                        optionSelected.value.price_type,
                        product
                    );
                } else {
                    optionPrice += getCustomizeOptionPrice(
                        optionSelected.value.price,
                        optionSelected.value.price_type,
                        product
                    );
                    optionPriceExclTax = optionPrice;
                }
            }
        } else {
            if (optionSelected.typeId == 'field') {
                if (!!optionSelected.value.final_price) {
                    optionPrice += getCustomizeOptionPrice(
                        optionSelected.field.final_price,
                        optionSelected.field.price_type,
                        product
                    );
                    optionPriceExclTax += getCustomizeOptionPrice(
                        optionSelected.field.price,
                        optionSelected.field.price_type,
                        product
                    );
                } else {
                    optionPrice += getCustomizeOptionPrice(
                        optionSelected.field.price,
                        optionSelected.field.price_type,
                        product
                    );
                    optionPriceExclTax = optionPrice;
                }
            } else if (optionSelected.typeId == 'file') {
                if (!!optionSelected.value.final_price) {
                    optionPrice += getCustomizeOptionPrice(
                        optionSelected.file.final_price,
                        optionSelected.file.price_type,
                        product
                    );
                    optionPriceExclTax += getCustomizeOptionPrice(
                        optionSelected.file.price,
                        optionSelected.file.price_type,
                        product
                    );
                } else {
                    optionPrice += getCustomizeOptionPrice(
                        optionSelected.file.price,
                        optionSelected.file.price_type,
                        product
                    );
                    optionPriceExclTax = optionPrice;
                }
            }
        }
    }
    return { optionPrice, optionPriceExclTax };
};
const FREE_PRICE = 0;

export const TAX_DISPLAY_TYPES = {
    EXCL_TAX: 1,
    INCL_TAX: 2,
    BOTH: 3
};

export const useProductPrice = props => {
    const {
        product,
        optionSelections,
        customizeOptions,
        optionCodes,
        quantity,
        productOptions
    } = props;

    const apolloClient = useApolloClient();
    const dataStoreConfig = apolloClient.readQuery({
        query: GET_STORE_CONFIG_DATA
    });

    const taxDisplayType = dataStoreConfig?.storeConfig?.tax_display_type;
    const isShowBoth = taxDisplayType === TAX_DISPLAY_TYPES.BOTH;

    const currency = useMemo(() => {
        return storage.getItem('store_view_currency');
    }, []);

    const productPrice = useMemo(
        () => getConfigPrice(product, optionCodes, optionSelections, quantity),
        [product, optionCodes, optionSelections, quantity]
    );

    const tierProductPirce = useMemo(() => {
        return filterQuantity(product, quantity);
    }, [product, quantity]);

    const customizeOptionPrice = useMemo(
        () =>
            getOptionPrice(
                productOptions ? productOptions : product,
                customizeOptions
            ),
        [product, customizeOptions]
    );

    const regular =
        (productPrice &&
            productPrice.regularPrice &&
            productPrice.regularPrice.amount &&
            productPrice.regularPrice.amount.value) ||
        FREE_PRICE;
    const maxValue =
        (productPrice &&
            productPrice.maximum_price &&
            productPrice.maximum_price.final_price &&
            productPrice.maximum_price.final_price.value +
                customizeOptionPrice.optionPrice) ||
        (regular || FREE_PRICE);
    const maxValueRegular =
        (productPrice &&
            productPrice.maximum_price &&
            productPrice.maximum_price.regular_price &&
            productPrice.maximum_price.regular_price.value +
                customizeOptionPrice.optionPrice) ||
        (regular || FREE_PRICE);
    const maxValueExclTax =
        productPrice?.maximum_price?.maximum_final_price_excl_tax &&
        productPrice.maximum_price.maximum_final_price_excl_tax.value +
            customizeOptionPrice.optionPriceExclTax;

    const minValue =
        (productPrice &&
            productPrice.minimum_price &&
            productPrice.minimum_price.final_price &&
            productPrice.minimum_price.final_price.value +
                customizeOptionPrice.optionPrice) ||
        (regular || FREE_PRICE);
    const minValueRegular =
        (productPrice &&
            productPrice.minimum_price &&
            productPrice.minimum_price.regular_price &&
            productPrice.minimum_price.regular_price.value +
                customizeOptionPrice.optionPrice) ||
        (regular || FREE_PRICE);
    const minValueExclTax =
        productPrice?.minimum_price?.minimum_final_price_excl_tax &&
        productPrice.minimum_price.minimum_final_price_excl_tax.value +
            customizeOptionPrice.optionPriceExclTax;

    const tierPrice =
        (tierProductPirce &&
            tierProductPirce?.final_price?.value +
                customizeOptionPrice.optionPrice) ||
        (regular || FREE_PRICE);

    const tierPriceExclTax =
        (tierProductPirce &&
            tierProductPirce?.final_price?.value +
                customizeOptionPrice.optionPriceExclTax) ||
        (regular || FREE_PRICE);

    const priceRanges = [];
    if (maxValue !== minValue) {
        if (minValue !== null && minValueRegular !== null) {
            if (tierPrice) {
                const toPriceValue = {
                    finalValue: tierPrice,
                    regularValue: minValueRegular,
                    finalValueExclTax: tierPriceExclTax
                };
                priceRanges.push(toPriceValue);
            } else {
                const fromPriceValue = {
                    finalValue: minValue,
                    regularValue: minValueRegular,
                    finalValueExclTax: minValueExclTax
                };
                priceRanges.push(fromPriceValue);
            }
        }
        if (maxValue !== null && maxValueRegular !== null) {
            if (tierPrice) {
                const toPriceValue = {
                    finalValue: tierPrice,
                    regularValue: maxValueRegular,
                    finalValueExclTax: tierPriceExclTax
                };
                priceRanges.push(toPriceValue);
            } else {
                const toPriceValue = {
                    finalValue: maxValue,
                    regularValue: maxValueRegular,
                    finalValueExclTax: maxValueExclTax
                };
                priceRanges.push(toPriceValue);
            }
        }
    } else {
        if (tierPrice) {
            priceRanges.push({
                finalValue: tierPrice,
                regularValue: minValueRegular,
                finalValueExclTax: tierPriceExclTax
            });
        } else {
            priceRanges.push({
                finalValue: minValue,
                regularValue: minValueRegular,
                finalValueExclTax: minValueExclTax
            });
        }
    }

    return {
        currency,
        productPrice,
        customizeOptionPrice,
        priceRanges,
        isShowBoth
    };
};
