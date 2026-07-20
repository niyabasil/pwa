import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { bool, func, shape, string } from 'prop-types';
import { Form } from 'informed';
import defaultClasses from './paymentInformation.module.css';
import { usePaymentInformation } from '../../talons/PaymentInformation/usePaymentInformation';
import PaymentMethods from './paymentMethods';

const PaymentInformation = props => {
    const { isUpdating, handlePlaceOrder } = props;

    const talonProps = usePaymentInformation(props);
    const classes = useStyle(defaultClasses, props.classes);

    const {
        handlePaymentError,
        handlePaymentSuccess,
        doneEditing,
        selectedPaymentMethodTitle,
        resetShouldSubmit,
        shouldPaymentSubmit
    } = talonProps;

    if (doneEditing && selectedPaymentMethodTitle) {
        return <span>{selectedPaymentMethodTitle}</span>;
    }

    return (
        <Form>
            <PaymentMethods
                onPaymentError={handlePaymentError}
                onPaymentSuccess={handlePaymentSuccess}
                shouldSubmit={shouldPaymentSubmit}
                resetShouldSubmit={resetShouldSubmit}
                handlePlaceOrder={handlePlaceOrder}
                isUpdating={isUpdating}
                classes={{
                    root: classes.paymentMethods
                }}
            />
        </Form>
    );
};

PaymentInformation.propTypes = {
    shouldPaymentSubmit: bool,
    isUpdating: bool,
    handlePlaceOrder: func,
    classes: shape({
        root: string
    })
};

export default PaymentInformation;
