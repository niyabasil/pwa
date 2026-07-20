import { useCallback, useMemo, useState } from 'react';
import { useAmOscContext } from '../../../context';
import useFieldState from '@magento/peregrine/lib/hooks/hook-wrappers/useInformedFieldStateWrapper';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from './giftWrap.gql';
import { useMutation } from '@apollo/client';
import { useCartContext } from '@magento/peregrine/lib/context/cart';

export const useGiftMessage = (props = {}) => {
    const { cartItems, orderGiftMessage } = props;

    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);

    const {
        addGiftMessageForWholeOrderMutation,
        addGiftMessageForItemsMutation
    } = operations;

    const [isOpenModal, setIsOpenModal] = useState(false);
    const { value: isChecked } = useFieldState('giftMessage');
    const [{ cartId }] = useCartContext();

    const [
        {
            amasty_checkout_sales_gift_options_allow_order: isAllowGiftMessageOrder,
            amasty_checkout_sales_gift_options_allow_items: isAllowGiftMessageItems
        }
    ] = useAmOscContext();

    // mutations

    const [
        addGiftMessageForWholeOrder,
        { loading: addGiftMessageForWholeOrderLoading }
    ] = useMutation(addGiftMessageForWholeOrderMutation);

    const [
        addGiftMessageForItems,
        { loading: addGiftMessageForItemsLoading }
    ] = useMutation(addGiftMessageForItemsMutation);

    const handleCloseModal = useCallback(() => {
        setIsOpenModal(false);
    }, [setIsOpenModal]);

    const handleOpenModal = useCallback(() => {
        setIsOpenModal(true);
    }, [setIsOpenModal]);

    const handleSubmit = useCallback(
        async formValues => {
            try {
                const { messageForOrder, messageForItems } = formValues;

                if (isAllowGiftMessageOrder) {
                    await addGiftMessageForWholeOrder({
                        variables: {
                            messageInput: {
                                cart_id: cartId,
                                ...messageForOrder
                            }
                        }
                    });
                }

                if (isAllowGiftMessageItems) {
                    await addGiftMessageForItems({
                        variables: {
                            cartId,
                            items: messageForItems
                        }
                    });
                }

                handleCloseModal();
            } catch {
                return;
            }
        },
        [
            cartId,
            addGiftMessageForWholeOrder,
            addGiftMessageForItems,
            handleCloseModal,
            isAllowGiftMessageItems,
            isAllowGiftMessageOrder
        ]
    );

    const handleChange = useCallback(
        ({ target }) => {
            if (target.checked) {
                handleOpenModal();
            }
        },
        [handleOpenModal]
    );

    const initialChecked =
        !!orderGiftMessage || cartItems.some(item => !!item.item_gift_message);

    const initialValues = useMemo(
        () => ({
            messageForOrder: orderGiftMessage || {},
            messageForItems: cartItems.map(item => {
                const { id, item_gift_message, product } = item;
                const message = item_gift_message || {};

                return {
                    ...message,
                    item_id: Number(id),
                    productName: product.name
                };
            })
        }),
        [orderGiftMessage, cartItems]
    );

    return {
        handleCloseModal,
        isOpenModal,
        handleOpenModal,
        isChecked,
        initialChecked,
        isAllowGiftMessageItems,
        isAllowGiftMessageOrder,
        handleSubmit,
        isDisabled:
            addGiftMessageForWholeOrderLoading || addGiftMessageForItemsLoading,
        initialValues,
        handleChange
    };
};
