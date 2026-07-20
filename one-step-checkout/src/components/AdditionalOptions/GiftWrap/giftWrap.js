import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { object, array } from 'prop-types';
import { Form } from 'informed';
import Checkbox from '@magento/venia-ui/lib/components/Checkbox';
import GiftMessage from './giftMessage';
import { useGiftWrap } from '../../../talons/AdditionalOptions/GiftWrap/useGiftWrap';
import { useIntl } from 'react-intl';

const GiftWrap = props => {
    const { checkoutInformationData } = props;
    const {
        amasty_gift_wrap: giftWrapData,
        items: cartItems,
        order_gift_message
    } = checkoutInformationData;
    const talonProps = useGiftWrap(props);
    const classes = useStyle({}, props.classes);
    const { formatMessage } = useIntl();

    const {
        isGiftWrapEnabled,
        isGiftMessageEnabled,
        giftWrapAmount,
        isUpdatingGiftWrap,
        handleUpdateGiftWrap
    } = talonProps;

    if (!isGiftWrapEnabled && !isGiftMessageEnabled) {
        return null;
    }

    const giftWrap = isGiftWrapEnabled ? (
        <Checkbox
            field={'giftWrap'}
            disabled={isUpdatingGiftWrap}
            onChange={handleUpdateGiftWrap}
            initialValue={!!giftWrapData.amount}
            label={formatMessage(
                {
                    id: 'amOsc.giftWrap',
                    defaultMessage: 'Gift wrap {giftWrapAmount}'
                },
                { giftWrapAmount }
            )}
        />
    ) : null;

    const giftMessage = isGiftMessageEnabled ? (
        <GiftMessage
            cartItems={cartItems}
            orderGiftMessage={order_gift_message}
        />
    ) : null;

    return (
        <Form className={classes.root}>
            {giftMessage}
            {giftWrap}
        </Form>
    );
};

GiftWrap.propTypes = {
    cartItems: array,
    giftWrapData: object
};

export default GiftWrap;
