import React, { Fragment, Suspense } from 'react';
import { shape, string, array } from 'prop-types';
import Checkbox from '@magento/venia-ui/lib/components/Checkbox';
import { useIntl } from 'react-intl';
import LinkButton from '@magento/venia-ui/lib/components/LinkButton';
import { useGiftMessage } from '../../../talons/AdditionalOptions/GiftWrap/useGiftMessage';
import GiftMessageModal from './giftMessageModal';

const GiftMessage = props => {
    const { cartItems, orderGiftMessage } = props;
    const talonProps = useGiftMessage({ cartItems, orderGiftMessage });
    const { formatMessage } = useIntl();

    const {
        handleCloseModal,
        isOpenModal,
        handleOpenModal,
        isChecked,
        isAllowGiftMessageOrder,
        isAllowGiftMessageItems,
        handleSubmit,
        isDisabled,
        isLoading,
        initialChecked,
        initialValues,
        handleChange
    } = talonProps;

    if (isLoading) {
        return null;
    }

    return (
        <Fragment>
            <Checkbox
                field={'giftMessage'}
                initialValue={initialChecked}
                onChange={handleChange}
                label={formatMessage(
                    {
                        id: 'amOsc.giftMessage',
                        defaultMessage: 'Add a gift message <link>[edit]</link>'
                    },
                    {
                        link: chunks =>
                            isChecked ? (
                                <LinkButton onClick={handleOpenModal}>
                                    {chunks}
                                </LinkButton>
                            ) : null
                    }
                )}
            />

            <Suspense fallback={null}>
                <GiftMessageModal
                    isOpen={isOpenModal}
                    closeModal={handleCloseModal}
                    isAllowGiftMessageOrder={isAllowGiftMessageOrder}
                    isAllowGiftMessageItems={isAllowGiftMessageItems}
                    onSubmit={handleSubmit}
                    isDisabled={isDisabled}
                    initialValues={initialValues}
                />
            </Suspense>
        </Fragment>
    );
};

GiftMessage.propTypes = {
    cartItems: array,
    classes: shape({
        root: string
    })
};

GiftMessage.defaultProps = {
    cartItems: []
};

export default GiftMessage;
