import React, { Fragment } from 'react';
import AmOrderDetails from './amOrderDetails';

const WrapOrderDetails = component => props => {
    const InnerComponent = component;

    return (
        <Fragment>
            <AmOrderDetails orderData={props.orderData} />
            <InnerComponent {...props} />
        </Fragment>
    );
};

export default WrapOrderDetails;
