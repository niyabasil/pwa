import React, { Fragment } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { bool, shape, string } from 'prop-types';
import defaultClasses from './shippingMethod.module.css';
import FormError from '@magento/venia-ui/lib/components/FormError';
import { useShippingMethod } from '../../talons/ShippingMethod/useShippingMethod';
import ShippingRadios from './shippingRadios';
import { Form } from 'informed';

const ShippingMethod = props => {
    const { isUpdating, design } = props;
    const talonProps = useShippingMethod(props);

    const {
        errors,
        isLoading,
        shippingMethods,
        handleChange,
        selectedShippingMethod,
        setFormApi
    } = talonProps;

    const classes = useStyle(defaultClasses, props.classes);

    return (
        <Fragment>
            <FormError errors={Array.from(errors.values())} />

            <Form
                getApi={setFormApi}
                className={classes.form}
                initialValues={{
                    shipping_method: selectedShippingMethod
                        ? selectedShippingMethod.serializedValue
                        : undefined
                }}
            >
                <ShippingRadios
                    disabled={isUpdating || isLoading}
                    shippingMethods={shippingMethods}
                    onChange={handleChange}
                    design={design}
                />
            </Form>
        </Fragment>
    );
};

ShippingMethod.propTypes = {
    blockTitle: string,
    isUpdating: bool,
    classes: shape({
        root: string
    })
};

export default ShippingMethod;
