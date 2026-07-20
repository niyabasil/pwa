import React from 'react';
import { useIntl } from 'react-intl';
import { arrayOf, bool, number, shape, string } from 'prop-types';

import { useStyle } from '@magento/venia-ui/lib/classify';
import RadioGroup from '@magento/venia-ui/lib/components/RadioGroup';
import ShippingRadio from './shippingRadio';
import defaultClasses from './shippingRadios.module.css';
import Radio from '@magento/venia-ui/lib/components/RadioGroup/radio';

const ShippingRadios = props => {
    const { disabled, shippingMethods, onChange, allowedfreeshippingData } = props;
    const { formatMessage } = useIntl();

    const classes = useStyle(defaultClasses, props.classes);
    const flagVal = null;

    const ERROR_MESSAGE = formatMessage({
        id: 'shippingRadios.errorLoading',
        defaultMessage:
            'Error loading shipping methods. Please ensure a shipping address is set and try again.'
    });

    if (!shippingMethods.length) {
        return <span className={classes.error}>{ERROR_MESSAGE}</span>;
    }

    const radioGroupClasses = {
        message: classes.radioMessage,
        radioLabel: classes.radioLabel,
        radioContainer: classes.radioContainer,
        root: !disabled ? classes.radioRoot : classes.radioRoot_disabled
    };

  //  const flagVal = 0;
    // const allowedfreeshippingStatus = allowedfreeshippingData.map(allowedshipping => {


    //  if(allowedshipping.product.allow_free_shipping == '1' )
    //     return allowedshipping.product.allow_free_shipping;


    // });
   

// alert(allowedfreeshippingDataValue);
    const shippingRadios = shippingMethods.map(method => {
        
        const allowedfreeshippingDataValue = allowedfreeshippingData.find(val => val.product.allow_free_shipping == '1');
//if(allowedfreeshippingDataValue && ){



//}

//alert(method.method_title);
if(allowedfreeshippingDataValue){
    const label = (
        <ShippingRadio
            currency={method.amount.currency}
            name={method.method_title}
            price={method.amount.value}
            title={method.carrier_title}
        />
    );
    const value = method.serializedValue;

    return (
        <Radio
            key={value}
            disabled={disabled || !method.available}
            label={label}
            classes={{
                label: classes.radioLabel,
                root: method.available
                    ? classes.radioContainer
                    : classes.radioContainer_notAvailable
            }}
            id={`shipping_method--${value}`}
            value={value}
        />
    );

    
}else if( method.method_title !== "Free") {
    const label = (
        <ShippingRadio
            currency={method.amount.currency}
            name={method.method_title}
            price={method.amount.value}
            title={method.carrier_title}
        />
    );
    const value = method.serializedValue;

    return (
        <Radio
            key={value}
            disabled={disabled || !method.available}
            label={label}
            classes={{
                label: classes.radioLabel,
                root: method.available
                    ? classes.radioContainer
                    : classes.radioContainer_notAvailable
            }}
            id={`shipping_method--${value}`}
            value={value}
        />
    );

}
   
    

    });

    return (
        <RadioGroup
            classes={radioGroupClasses}
            disabled={disabled}
            field="shipping_method"
            id={'shippingMethod'}
            // items={shippingRadios}
            onChange={onChange}
        >
            {shippingRadios}
        </RadioGroup>
    );
};

export default ShippingRadios;

ShippingRadios.propTypes = {
    classes: shape({
        error: string,
        radioMessage: string,
        radioLabel: string,
        radioRoot: string
    }),
    disabled: bool,
    shippingMethods: arrayOf(
        shape({
            amount: shape({
                currency: string,
                value: number
            }),
            available: bool,
            carrier_code: string,
            carrier_title: string,
            method_code: string,
            method_title: string,
            serializedValue: string.isRequired
        })
    ).isRequired
};
