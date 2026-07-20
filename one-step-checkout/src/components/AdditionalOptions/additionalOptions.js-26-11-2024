import React, { Suspense } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { func, shape, string } from 'prop-types';
import defaultClasses from './additionalOptions.module.css';
import Agreements from './Agreements';
import { Form } from 'informed';
import Checkbox from '@magento/venia-ui/lib/components/Checkbox';
import { useIntl } from 'react-intl';
import Field from '@magento/venia-ui/lib/components/Field';
import TextArea from '@magento/venia-ui/lib/components/TextArea';
import { Accordion, Section } from '@magento/venia-ui/lib/components/Accordion';
import { useAdditionalOptions } from '../../talons/AdditionalOptions/useAdditionalOptions';
import GiftCardSection from '@magento/venia-ui/lib/components/CartPage/PriceAdjustments/giftCardSection';
import GiftWrap from './GiftWrap';

const CouponCode = React.lazy(() =>
    import('@magento/venia-ui/lib/components/CartPage/PriceAdjustments/CouponCode')
);

const AdditionalOptions = props => {
    const { setIsUpdating } = props;
    const talonProps = useAdditionalOptions(props);
    const classes = useStyle(defaultClasses, props.classes);
    const { formatMessage } = useIntl();

    const {
        isCouponCodeEnabled,
        isNewsletterEnabled,
        isNewsletterChecked,
        isOrderCommentEnabled,
        isAgreementsEnabled,
        isCreateAccountEnabled,
        isCreateAccountChecked,
        agreementFormProps,
        additionalFieldsFormProps
    } = talonProps;

    const agreements = isAgreementsEnabled ? (
        <Form className={classes.innerRoot} {...agreementFormProps}>
            <Agreements />
        </Form>
    ) : null;

    const newsletter = isNewsletterEnabled ? (
        <Checkbox
            field={'is_subscribe'}
            initialValue={isNewsletterChecked}
            label={formatMessage({
                id: 'amOsc.newsletter',
                defaultMessage: 'Sign Up for Our Newsletter'
            })}
        />
    ) : null;

    const orderComment = isOrderCommentEnabled ? (
        <Field
            id={'comment'}
            label={formatMessage({
                id: 'amOsc.orderComment',
                defaultMessage: 'Order Comment'
            })}
            optional
        >
            <TextArea id={'comment'} field={'comment'} rows={2} />
        </Field>
    ) : null;

    const couponCode = isCouponCodeEnabled ? (
        <Section
            id={'coupon_code'}
            title={formatMessage({
                id: 'priceAdjustments.couponCode',
                defaultMessage: 'Enter Coupon Code'
            })}
        >
            <Suspense fallback={null}>
                <CouponCode setIsCartUpdating={setIsUpdating} />
            </Suspense>
        </Section>
    ) : null;

    const register = isCreateAccountEnabled ? (
        <Checkbox
            field={'is_register'}
            initialValue={isCreateAccountChecked}
            label={formatMessage({
                id: 'amOsc.createAnAccount',
                defaultMessage: 'Create an Account'
            })}
        />
    ) : null;

    return (
        <div className={classes.root}>
            {agreements}
            <GiftWrap {...props} classes={{ root: classes.innerRoot }} />

            <Form {...additionalFieldsFormProps} className={classes.innerRoot}>
                {newsletter}
                {register}
                {orderComment}
            </Form>

            <Accordion
                classes={{
                    root: classes.accordion
                }}
                canOpenMultiple={true}
            >
                {couponCode}
                <GiftCardSection setIsCartUpdating={setIsUpdating} />
            </Accordion>
        </div>
    );
};

AdditionalOptions.propTypes = {
    setIsUpdating: func,
    classes: shape({
        root: string
    })
};

export default AdditionalOptions;
