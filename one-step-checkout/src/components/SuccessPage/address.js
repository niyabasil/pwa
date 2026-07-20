import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { object, shape, string } from 'prop-types';
import defaultClasses from './successPage.module.css';
import { deserializeCustomAttributes } from '../../utils';
import { useAmOscContext } from '../../context';

const Address = props => {
    const { address } = props;
    const [{ checkoutFields }] = useAmOscContext();
    const classes = useStyle(defaultClasses, props.classes);

    const {
        email,
        city,
        country,
        firstname,
        lastname,
        postcode,
        region,
        street,
        telephone,
        custom_attributes
    } = address;

    const nameString = `${firstname} ${lastname}`;
    const streetRows = street.map((row, index) => {
        return <span key={index}>{row}</span>;
    });
    const additionalAddressString = `${city}, ${region.label} ${postcode}`;
    const customAttributes = Object.values(
        deserializeCustomAttributes(custom_attributes)
    );
    const customAttributesRows = customAttributes.length
        ? customAttributes.map(attr => {
              if (!attr.value || attr.value === '-') {
                  return null;
              }

              const field = checkoutFields.find(
                  f => f.attribute_code === attr.attribute_code
              );
              const value = `${field ? `${field.label}: ` : ''}${attr.value}`;
              return <span key={attr.attribute_code}>{value}</span>;
          })
        : null;

    return (
        <div className={classes.address}>
            <span className={classes.email}>{email}</span>
            <span className={classes.name}>{nameString}</span>
            {streetRows}
            <span>{additionalAddressString}</span>
            <span>{country.label}</span>
            <a className={classes.phone} href={`tel: ${telephone}`}>
                {telephone}
            </a>
            {customAttributesRows}
        </div>
    );
};

Address.propTypes = {
    email: string,
    address: object,
    classes: shape({
        root: string
    })
};

export default Address;
