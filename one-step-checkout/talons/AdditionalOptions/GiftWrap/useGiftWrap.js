import { useCallback } from 'react';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import { useAmOscContext } from '../../../context';
import { useCartContext } from '@magento/peregrine/lib/context/cart';
import { useMutation } from '@apollo/client';
import { useIntl } from 'react-intl';
import DEFAULT_OPERATIONS from './giftWrap.gql';

export const formatPrice = (value, currency = 'USD', locale = 'en') => {
    const numberFormat = new Intl.NumberFormat(locale, {
        style: 'currency',
        currency
    });

    return numberFormat.format(value);
};

export const useGiftWrap = (props = {}) => {
    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const { updateGiftWrapInformationMutation } = operations;

    const [
        {
            amasty_checkout_gifts_gift_wrap: isGiftWrapEnabled,
            amasty_checkout_gifts_gift_wrap_fee,
            cartCurrency,
            amasty_checkout_sales_gift_options_allow_order: isAllowGiftMessageOrder,
            amasty_checkout_sales_gift_options_allow_items: isAllowGiftMessageItems
        },
        { setIsUpdating }
    ] = useAmOscContext();

    const { locale } = useIntl();

    const giftWrapAmount = formatPrice(
        amasty_checkout_gifts_gift_wrap_fee,
        cartCurrency,
        locale
    );

    const [{ cartId }] = useCartContext();

    const [updateGiftWrap, { loading: isUpdatingGiftWrap }] = useMutation(
        updateGiftWrapInformationMutation
    );

    const handleUpdateGiftWrap = useCallback(
        async e => {
            const { checked } = e.target;

            try {
                setIsUpdating(true);
                await updateGiftWrap({
                    variables: {
                        cartId,
                        checked
                    }
                });
            } catch (err) {
                return;
            }

            setIsUpdating(false);
        },
        [cartId, updateGiftWrap, setIsUpdating]
    );

    return {
        isGiftWrapEnabled,
        isGiftMessageEnabled:
            isAllowGiftMessageOrder || isAllowGiftMessageItems,
        isUpdatingGiftWrap,
        isAllowGiftMessageOrder,
        isAllowGiftMessageItems,
        handleUpdateGiftWrap,
        giftWrapAmount
    };
};
