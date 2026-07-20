import React, { Suspense } from 'react';
import { bool, shape, string } from 'prop-types';

const AddressBook = React.lazy(() => import('./AddressBook'));
const GuestForm = React.lazy(() => import('./guestForm'));

const ShippingInformation = props => {
    const { isGuestCheckout } = props;

    const shippingInformation = isGuestCheckout ? (
        <GuestForm {...props} />
    ) : (
        <AddressBook {...props} />
    );

    return <Suspense fallback={null}>{shippingInformation}</Suspense>;
};

ShippingInformation.propTypes = {
    isGuestCheckout: bool,
    classes: shape({
        root: string
    })
};

export default ShippingInformation;
