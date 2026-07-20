import React from 'react';
import { bool, func, object, shape, string, array } from 'prop-types';
import Dialog from '@magento/venia-ui/lib/components/Dialog';
import { useIntl } from 'react-intl';
import GiftMessageSection from './giftMessageSection';
import GiftMessageItems from './giftMessageItems';

const GiftMessageModal = props => {
    const {
        closeModal,
        isOpen,
        initialValues,
        isAllowGiftMessageOrder,
        isAllowGiftMessageItems,
        onSubmit,
        isDisabled
    } = props;

    const { formatMessage } = useIntl();

    const orderSection = isAllowGiftMessageOrder ? (
        <GiftMessageSection field={'messageForOrder'} />
    ) : null;
    const itemsSection = isAllowGiftMessageItems ? (
        <GiftMessageItems field={'messageForItems'} />
    ) : null;

    return (
        <Dialog
            isOpen={isOpen}
            onCancel={closeModal}
            shouldUnmountOnHide={true}
            shouldDisableAllButtons={isDisabled}
            onConfirm={onSubmit}
            title={formatMessage({
                id: 'amOsc.gifrMessageModalTitle',
                defaultMessage: 'Gift Messages'
            })}
            confirmTranslationId={'amOsc.update'}
            confirmText={'Update'}
            formProps={{ initialValues }}
        >
            {itemsSection}
            {orderSection}
        </Dialog>
    );
};

GiftMessageModal.propTypes = {
    closeModal: func,
    isOpen: bool,
    initialValues: shape({
        messageForOrder: object,
        messageForItems: array
    }),
    isAllowGiftMessageOrder: bool,
    isAllowGiftMessageItems: bool,
    onSubmit: func,
    isDisabled: bool,
    classes: shape({
        root: string
    })
};

export default GiftMessageModal;
