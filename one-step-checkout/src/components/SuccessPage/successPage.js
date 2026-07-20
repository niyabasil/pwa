import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { object, shape, string } from 'prop-types';
import defaultClasses from './successPage.module.css';
import { FormattedMessage, useIntl } from 'react-intl';
import { StoreTitle } from '@magento/venia-ui/lib/components/Head';
import ItemsReview from './ItemsReview';
import { useSuccessPage } from '../../talons/SuccessPage/useSuccessPage';
import Address from './address';
import Delivery from './delivery';
import CmsBlock from '../CmsBlock';
import PriceSummary from '../OrderSummary/PriceSummary';
import CreateAccount from '@magento/venia-ui/lib/components/CheckoutPage/OrderConfirmationPage/createAccount';
import Button from '@magento/venia-ui/lib/components/Button';
import LinkButton from '@magento/venia-ui/lib/components/LinkButton';
import OrderComment from './orderComment';
import GiftMessage from './giftMessage';

const SuccessPage = props => {
    const { data } = props;
    const talonProps = useSuccessPage(props);
    const classes = useStyle(defaultClasses, props.classes);
    const { formatMessage } = useIntl();

    const {
        shippingMethod,
        shippingAddress,
        billingAddress,
        paymentMethod,
        deliveryData,
        orderNumber,
        orderGiftMessage,
        createdAt,
        isSubscribed,
        isRegistered,
        orderComment,
        bottomBlockId,
        isShowCreateAccountForm,
        handleClickContinueShopping,
        setView,
        view
    } = talonProps;

    const subscriptionMessage = isSubscribed ? (
        <div className={classes.message}>
            <FormattedMessage
                id={'amOsc.subscriptionMessage'}
                defaultMessage={'Thank you for your subscription.'}
            />
        </div>
    ) : null;

    const registrationMessage = isRegistered ? (
        <div className={classes.message}>
            <FormattedMessage
                id={'amOsc.registrationMessage'}
                defaultMessage={
                    'Registration: A letter with further instructions will be sent to your email.'
                }
            />
        </div>
    ) : null;

    const createAccountArea = isShowCreateAccountForm ? (
        <div className={classes.createAccount}>
            <p>
                <FormattedMessage
                    id={'amOsc.successPageCreateAccountMessage'}
                    defaultMessage={
                        'You can track your order status by creating an account.'
                    }
                />
            </p>
            <p>
                <FormattedMessage
                    id={'amOsc.successPageEmail'}
                    defaultMessage={'Email Address: {email}'}
                    values={{ email: shippingAddress.email }}
                />
            </p>
            <Button onClick={() => setView('register')}>
                <FormattedMessage
                    id={'amOsc.successPageCreateAccountBtn'}
                    defaultMessage={'Create An Account'}
                />
            </Button>
        </div>
    ) : null;

    const comment = orderComment ? <OrderComment data={orderComment} /> : null;
    const footer = bottomBlockId ? <CmsBlock id={bottomBlockId} /> : null;

    const containerClass =
        view === 'order' ? classes.mainContainer : classes.mainContainer_hidden;
    const createAccountFormClass =
        view === 'register'
            ? classes.createAccountForm
            : classes.createAccountForm_hidden;

    return (
        <div className={classes.root}>
            <StoreTitle>
                {formatMessage(
                    {
                        id: 'amOsc.successPageTitle',
                        defaultMessage: 'Order #{orderNumber}'
                    },
                    { orderNumber }
                )}
            </StoreTitle>
            <div className={containerClass}>
                <h1 className={classes.heading}>
                    <FormattedMessage
                        id={'amOsc.thankYou'}
                        defaultMessage={'Thank you for your purchase!'}
                    />
                </h1>

                {subscriptionMessage}
                {registrationMessage}

                <div className={classes.orderNumber}>
                    <FormattedMessage
                        id={'amOsc.orderNumber'}
                        defaultMessage={'Your order is: #'}
                    />
                    <span>{orderNumber}</span>
                </div>

                <div className={classes.additionalText}>
                    <FormattedMessage
                        id={'amOsc.additionalText'}
                        defaultMessage={
                            "We'll email you an order confirmation with details and tracking info."
                        }
                    />
                </div>

                <div className={classes.section}>
                    <div className={classes.sectionTitle}>
                        <FormattedMessage
                            id={'amOsc.orderDetails'}
                            defaultMessage={'Order details:'}
                        />
                    </div>

                    <div className={classes.date}>
                        <FormattedMessage
                            id={'amOsc.orderDate'}
                            defaultMessage={'Order Date: {date}'}
                            values={{ date: createdAt }}
                        />
                    </div>
                </div>

                <div className={classes.shippingAndPaymentInfo}>
                    <div className={classes.block}>
                        <div className={classes.blockHeading}>
                            <FormattedMessage
                                id={'amOsc.shippingAddress'}
                                defaultMessage={'Shipping Address'}
                            />
                        </div>

                        <Address address={shippingAddress} />
                    </div>
                    <div className={classes.block}>
                        <div className={classes.blockHeading}>
                            <FormattedMessage
                                id={'amOsc.shippingMethod'}
                                defaultMessage={'Shipping Method'}
                            />
                        </div>
                        <div className={classes.blockContent}>
                            {shippingMethod}
                        </div>
                    </div>

                    <div className={classes.block}>
                        <div className={classes.blockHeading}>
                            <FormattedMessage
                                id={'amOsc.billingAddress'}
                                defaultMessage={'Billing Address'}
                            />
                        </div>

                        <Address address={billingAddress} />
                    </div>

                    <div className={classes.block}>
                        <div className={classes.blockHeading}>
                            <FormattedMessage
                                id={'amOsc.paymentMethod'}
                                defaultMessage={'Payment Method'}
                            />
                        </div>
                        <div className={classes.blockContent}>
                            {paymentMethod}
                        </div>
                    </div>
                </div>

                {comment}

                <Delivery data={deliveryData} />

                <div className={classes.itemsReview}>
                    <div className={classes.items}>
                        <ItemsReview
                            data={data}
                            classes={{ items_container: classes.itemsList }}
                        />
                        <GiftMessage data={orderGiftMessage} />
                    </div>

                    <PriceSummary
                        classes={{ root: classes.summary }}
                        orderData={data}
                    />
                </div>

                {footer}

                <Button priority={'high'} onClick={handleClickContinueShopping}>
                    <FormattedMessage
                        id={'amOsc.continueShopping'}
                        defaultMessage={'Continue Shopping'}
                    />
                </Button>

                {createAccountArea}
            </div>

            <div className={createAccountFormClass}>
                <CreateAccount
                    firstname={shippingAddress.firstname}
                    lastname={shippingAddress.lastname}
                    email={shippingAddress.email}
                    classes={{ root: classes.createAccountFormRoot }}
                />
                <LinkButton onClick={() => setView('order')}>
                    <FormattedMessage
                        id={'amOsc.cancel'}
                        defaultMessage={'Cancel'}
                    />
                </LinkButton>
            </div>
        </div>
    );
};

SuccessPage.propTypes = {
    data: object,
    classes: shape({
        root: string
    })
};

export default SuccessPage;
