import React, { Fragment, useMemo } from 'react';
import { shape, string, array } from 'prop-types';
import { deserializeCustomAttributes } from '../../utils';
import { gql, useQuery } from '@apollo/client';

const GET_OSC_FIELDS = gql`
    query getAmastyOscFields {
        storeConfig {
            store_code
            amasty_checkout_manage_checkout_fields {
                amasty_checkout_checkout_fields
            }
        }
    }
`;

const AmAddress = props => {
    const { data } = props;

    const { data: storeConfigData, loading } = useQuery(GET_OSC_FIELDS);

    const checkoutFields = useMemo(() => {
        const { storeConfig } = storeConfigData || {};
        const { amasty_checkout_manage_checkout_fields } = storeConfig || {};
        const { amasty_checkout_checkout_fields } =
            amasty_checkout_manage_checkout_fields || {};
        try {
            return Object.values(JSON.parse(amasty_checkout_checkout_fields));
        } catch {
            return [];
        }
    }, [storeConfigData]);

    if (loading) {
        return null;
    }

    const { custom_attributes, telephone, vat_id, fax } = data;
    const attributesArr = custom_attributes || [];
    const customAttributes = Object.values(
        deserializeCustomAttributes([
            { attribute_code: 'fax', value: fax },
            { attribute_code: 'vat_id', value: vat_id },
            ...attributesArr
        ])
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

    const phoneRow = telephone ? (
        <a href={`tel: ${telephone}`}>{telephone}</a>
    ) : null;

    return (
        <Fragment>
            {phoneRow}
            {customAttributesRows}
        </Fragment>
    );
};

AmAddress.propTypes = {
    data: shape({
        telephone: string,
        custom_attributes: array
    }),
    classes: shape({
        root: string
    })
};

export default AmAddress;
