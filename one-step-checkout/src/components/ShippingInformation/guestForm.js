import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string, bool } from 'prop-types';
import GuestFields from './GuestFields';
import { useAsyncAddressForm } from '../../talons/ShippingInformation/useAsyncAddressForm';
import AddressForm from './AddressForm';
import { Form } from 'informed';

const GuestForm = props => {
    const { isGuestCheckout } = props;
    const { shippingData, optionsFormProps } = useAsyncAddressForm(props);

    const classes = useStyle({}, props.classes);
    const guestFields = isGuestCheckout ? (
        <Form>
            <GuestFields {...props} />
        </Form>
    ) : null;

    return (
        <div className={classes.root}>
            {guestFields}
            <AddressForm
                shippingData={shippingData}
                isGuestCheckout={isGuestCheckout}
                optionsFormProps={optionsFormProps}
                asyncForm
            />
        </div>
    );
};

GuestForm.propTypes = {
    isGuestCheckout: bool,
    classes: shape({
        root: string
    })
};

export default GuestForm;
