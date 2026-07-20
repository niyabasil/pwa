import React from 'react';
import { bool, func, shape, string } from 'prop-types';
import Dialog from '@magento/venia-ui/lib/components/Dialog';
import RichText from '@magento/venia-ui/lib/components/RichText';

const AgreementModal = props => {
    const { closeModal, isOpen, activeItem } = props;

    const { content, name } = activeItem || {};

    return (
        <Dialog
            isOpen={isOpen}
            onCancel={closeModal}
            shouldUnmountOnHide={true}
            title={name}
            shouldShowButtons={false}
        >
            <RichText content={content} />
        </Dialog>
    );
};

AgreementModal.propTypes = {
    closeModal: func,
    isOpen: bool,
    activeItem: shape({
        content: string,
        name: string
    })
};

export default AgreementModal;
