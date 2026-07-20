import { useCallback, useEffect, useState } from 'react';
import { useUserContext } from '@magento/peregrine/lib/context/user';
import { useAmOscContext } from '../../context';
import { useHistory } from 'react-router-dom';

export const useSuccessPage = props => {
    const { data, order } = props;

    const [view, changeView] = useState('order');
    const [{ isSignedIn }] = useUserContext();
    const history = useHistory();
    const [
        {
            amasty_checkout_success_page_block_id: bottomBlockId,
            amasty_checkout_additional_options_create_account: createAccountOption,
            isGuestEmailAvailable
        }
    ] = useAmOscContext();

    const handleClickContinueShopping = useCallback(() => history.push('/'), [
        history
    ]);
    const setView = useCallback((newView = 'order') => changeView(newView), [
        changeView
    ]);

    useEffect(() => {
        const { scrollTo } = globalThis;

        if (typeof scrollTo === 'function') {
            scrollTo({
                left: 0,
                top: 0,
                behavior: 'smooth'
            });
        }
    }, [view]);

    const { cart, getAdditionalFields } = data;
    const {
        email,
        shipping_addresses,
        billing_address,
        amasty_delivery_date,
        selected_payment_method,
        order_gift_message
    } = cart;

    const address = shipping_addresses[0];

    const shippingMethod = `${
        address.selected_shipping_method.carrier_title
    } - ${address.selected_shipping_method.method_title}`;

    const { order_number: orderNumber, amasty_order_date: createdAt } = order;
    const { is_subscribe, is_register, comment } = getAdditionalFields || {};

    return {
        orderNumber,
        createdAt,
        shippingMethod,
        paymentMethod: selected_payment_method.title,
        shippingAddress: { ...address, email },
        billingAddress: { ...billing_address, email },
        deliveryData: amasty_delivery_date,
        isSubscribed: is_subscribe,
        isRegistered: is_register,
        orderComment: comment,
        orderGiftMessage: order_gift_message,
        bottomBlockId,
        isShowCreateAccountForm:
            !isSignedIn &&
            (!createAccountOption ||
                (createAccountOption === 1 && !is_register)) &&
            isGuestEmailAvailable,
        handleClickContinueShopping,
        view,
        setView
    };
};
