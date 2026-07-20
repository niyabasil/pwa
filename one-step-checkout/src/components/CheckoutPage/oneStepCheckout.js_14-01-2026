import React, { useCallback, useEffect } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';
import { useCheckoutPage } from '../../talons/CheckoutPage/useCheckoutPage';
import { StoreTitle } from '@magento/venia-ui/lib/components/Head';
import { FormattedMessage, useIntl } from 'react-intl';
import { fullPageLoadingIndicator } from '@magento/venia-ui/lib/components/LoadingIndicator';
import GuestSignIn from '@magento/venia-ui/lib/components/CheckoutPage/GuestSignIn';
import defaultClasses from './oneStepCheckout.module.css';
import FormError from '@magento/venia-ui/lib/components/FormError';
import payments from '@magento/venia-ui/lib/components/CheckoutPage/PaymentInformation/paymentMethodCollection';
import Button from '@magento/venia-ui/lib/components/Button';
import CmsBlock from '../CmsBlock';
import SuccessPage from '../SuccessPage';
import CheckoutContent from './checkoutContent';
import { useToasts } from '@magento/peregrine';
import Icon from '@magento/venia-ui/lib/components/Icon';
import { AlertCircle as AlertCircleIcon } from 'react-feather';

const errorIcon = <Icon src={AlertCircleIcon} size={20} />;

const OneStepCheckout = props => {
    const { classes: propClasses, amAllowGuestCheckout } = props;
    const { formatMessage } = useIntl();
    const [, { addToast }] = useToasts();

    const onSuccessCreateAccount = useCallback(() => {
        addToast({
            type: 'info',
            message: formatMessage({
                id: 'checkoutPage.accountSuccessfullyCreated',
                defaultMessage: 'Account successfully created.'
            }),
            timeout: 5000
        });
    }, [addToast, formatMessage]);

    const talonProps = useCheckoutPage({
        amAllowGuestCheckout,
        onSuccessCreateAccount
    });

    const {
        pageTitle,
        pageDescription,
        isLoading,
        isGuestCheckout,
        toggleSignInContent,
        activeContent,
        isCartEmpty,
        availablePaymentMethods,
        topBlockId,
        bottomBlockId,
        orderDetailsData,
        order,
        isPlacingOrder,
        isUpdating,
        colorScheme,
        isDisabledCheckout,
        hasError,
        error,
        ...restProps
    } = talonProps;

    const classes = useStyle(defaultClasses, propClasses);

    useEffect(() => {
        if (hasError) {
            const message =
                error && error.message
                    ? error.message
                    : formatMessage({
                          id: 'checkoutPage.errorSubmit',
                          defaultMessage:
                              'Oops! An error occurred while submitting. Please try again.'
                      });
            addToast({
                type: 'error',
                icon: errorIcon,
                message,
                dismissable: true,
                timeout: 7000
            });

            if (process.env.NODE_ENV !== 'production') {
                console.error(error);
            }
        }
    }, [addToast, error, formatMessage, hasError]);

    if (isDisabledCheckout) {
        return (
            <div className={classes.root}>
                <GuestSignIn
                    classes={{ contentContainer: classes.disabledGuest }}
                    toggleActiveContent={toggleSignInContent}
                    isActive
                />
            </div>
        );
    }

    if (order && orderDetailsData) {
        return <SuccessPage data={orderDetailsData} order={order} />;
    }

    if (isLoading) {
        return fullPageLoadingIndicator;
    } else if (isCartEmpty) {
        return (
            <div className={classes.root}>
                <div className={classes.empty_cart_container}>
                    <div className={classes.heading_container}>
                        <h1 className={classes.heading}>{pageTitle}</h1>
                    </div>
                    <h3>
                        <FormattedMessage
                            id={'checkoutPage.emptyMessage'}
                            defaultMessage={'There are no items in your cart.'}
                        />
                    </h3>
                </div>
            </div>
        );
    }

    const signInElement = isGuestCheckout ? (
        <GuestSignIn
            isActive={activeContent === 'signIn'}
            toggleActiveContent={toggleSignInContent}
        />
    ) : null;

    const signInContainerElement = isGuestCheckout ? (
        <Button
            className={classes.signInButton}
            onClick={toggleSignInContent}
            priority="normal"
            disabled={isPlacingOrder || isUpdating}
        >
            <FormattedMessage
                id={'checkoutPage.signInButton'}
                defaultMessage={'Sign In'}
            />
        </Button>
    ) : null;

    const formErrors = [];
    const paymentMethods = Object.keys(payments);

    // If we have an implementation, or if this is a "zero" checkout,
    // we can allow checkout to proceed.
    const isPaymentAvailable =
        availablePaymentMethods &&
        !!availablePaymentMethods.find(
            ({ code }) => code === 'free' || paymentMethods.includes(code)
        );

    if (!isPaymentAvailable) {
        formErrors.push(
            new Error(
                formatMessage({
                    id: 'checkoutPage.noPaymentAvailable',
                    defaultMessage: 'Payment is currently unavailable.'
                })
            )
        );
    }

    const header = topBlockId ? (
        <CmsBlock classes={{ root: classes.topBlock }} id={topBlockId} />
    ) : null;
    const footer = bottomBlockId ? <CmsBlock id={bottomBlockId} /> : null;

    const contentClasses =
        activeContent === 'checkout'
            ? classes.checkoutContent
            : classes.checkoutContent_hidden;

    return (
        <div style={colorScheme} className={classes.root}>
            <StoreTitle>{pageTitle}</StoreTitle>

            <div className={contentClasses}>
                {header}

                <FormError
                    classes={{ root: classes.formErrors }}
                    errors={formErrors}
                />

                <div className={classes.heading_container}>
                    <div>
                        <h1 className={classes.heading}>{pageTitle}</h1>
                        <p className={classes.description}>{pageDescription}</p>
                    </div>
                    {signInContainerElement}
                </div>

                <CheckoutContent
                    isGuestCheckout={isGuestCheckout}
                    toggleSignInContent={toggleSignInContent}
                    isUpdating={isUpdating}
                    isPlacingOrder={isPlacingOrder}
                    {...restProps}
                />

                {footer}
            </div>

            {signInElement}
        </div>
    );
};

OneStepCheckout.propTypes = {
    classes: shape({
        root: string
    })
};

export default OneStepCheckout;
