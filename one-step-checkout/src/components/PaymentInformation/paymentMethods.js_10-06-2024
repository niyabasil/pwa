import React, { Fragment } from 'react';
import { shape, string, bool, func } from 'prop-types';
import { useIntl } from 'react-intl';
import { usePaymentMethods } from '../../talons/PaymentInformation/usePaymentMethods';
import { useStyle } from '@magento/venia-ui/lib/classify';
import RadioGroup from '@magento/venia-ui/lib/components/RadioGroup';
import Radio from '@magento/venia-ui/lib/components/RadioGroup/radio';
import defaultClasses from './paymentMethods.module.css';
import payments from '@magento/venia-ui/lib/components/CheckoutPage/PaymentInformation/paymentMethodCollection';
import PlaceOrderButton from '../OrderSummary/placeOrderButton';
import Agreements from '../AdditionalOptions/Agreements';

const PaymentMethods = props => {
    const {
        classes: propClasses,
        onPaymentError,
        onPaymentSuccess,
        resetShouldSubmit,
        shouldSubmit,
        handlePlaceOrder,
        isUpdating
    } = props;

    const { formatMessage } = useIntl();

    const classes = useStyle(defaultClasses, propClasses);

    const talonProps = usePaymentMethods();

    const {
        availablePaymentMethods,
        currentSelectedPaymentMethod,
        initialSelectedMethod,
        isLoading,
        isShowPlaceOrderButton,
        isShowAgreements
    } = talonProps;

    if (isLoading) {
        return null;
    }

    const placeOrderButton = isShowPlaceOrderButton ? (
        <PlaceOrderButton
            handlePlaceOrder={handlePlaceOrder}
            isUpdating={isUpdating}
        />
    ) : null;

    const agreements = isShowAgreements ? (
        <div className={classes.agreements}>
            <Agreements />
        </div>
    ) : null;

    const radios = availablePaymentMethods
        .map(({ code, title }) => {
            // If we don't have an implementation for a method type, ignore it.
            if (!Object.keys(payments).includes(code)) {
                return;
            }

            const id = `paymentMethod--${code}`;
            const isSelected = currentSelectedPaymentMethod === code;
            const PaymentMethodComponent = payments[code];
            const renderedComponent = isSelected ? (
                <Fragment>
                    <PaymentMethodComponent
                        onPaymentSuccess={onPaymentSuccess}
                        onPaymentError={onPaymentError}
                        resetShouldSubmit={resetShouldSubmit}
                        shouldSubmit={shouldSubmit}
                    />

                    {agreements}
                    {placeOrderButton}
                </Fragment>
            ) : null;

            return (
                <div key={code} className={classes.payment_method}>
                    <Radio
                        id={id}
                        label={title}
                        value={code}
                        classes={{
                            label: classes.radio_label
                        }}
                        checked={isSelected}
                    />
                    {renderedComponent}
                </div>
            );
        })
        .filter(paymentMethod => !!paymentMethod);

    const noPaymentMethodMessage = !radios.length ? (
        <div className={classes.payment_errors}>
            <span>
                {formatMessage({
                    id: 'checkoutPage.paymentLoadingError',
                    defaultMessage: 'There was an error loading payments.'
                })}
            </span>
            <span>
                {formatMessage({
                    id: 'checkoutPage.refreshOrTryAgainLater',
                    defaultMessage: 'Please refresh or try again later.'
                })}
            </span>
        </div>
    ) : null;

    const rootClassName = isUpdating ? classes.root_disabled : classes.root;

    return (
        <div className={rootClassName}>
            <RadioGroup
                classes={{ root: classes.radio_group }}
                field="selectedPaymentMethod"
                initialValue={initialSelectedMethod}
            >
                {radios}
            </RadioGroup>
            {noPaymentMethodMessage}
        </div>
    );
};

export default PaymentMethods;

PaymentMethods.propTypes = {
    classes: shape({
        root: string,
        payment_method: string,
        radio_label: string
    }),
    onPaymentSuccess: func,
    onPaymentError: func,
    resetShouldSubmit: func,
    selectedPaymentMethod: string,
    shouldSubmit: bool
};
