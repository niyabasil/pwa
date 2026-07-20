import React, { useCallback, useMemo } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string, func, bool } from 'prop-types';
import { useIntl } from 'react-intl';
import Checkbox from '@magento/venia-ui/lib/components/Checkbox';
import Field from '../ShippingInformation/AddressForm/field';
import FormError from '@magento/venia-ui/lib/components/FormError';
import { isRequired } from '@magento/venia-ui/lib/util/formValidators';

import defaultClasses from './billingAddress.module.css';

import { useBillingAddress } from '../../talons/BillingAddress/useBillingAddress';
import { getFieldKey } from '../../utils';
import ScrollAnchor from '@magento/venia-ui/lib/components/ScrollAnchor/scrollAnchor';

const BillingAddress = props => {
    const classes = useStyle(defaultClasses, props.classes);

    const {
        isBillingAddressSame,
        initialValues,
        errors,
        fields,
        blockRef
    } = useBillingAddress(props);

    const { formatMessage } = useIntl();

    /**
     * These 2 functions are wrappers around the `isRequired` function
     * of `formValidators`. They perform validations only if the
     * billing address is different from shipping address.
     */
    const isFieldRequired = useCallback((value, { isBillingAddressSame }) => {
        if (isBillingAddressSame) {
            /**
             * Informed validator functions return `undefined` if
             * validation is `true`
             */
            return undefined;
        } else {
            return isRequired(value);
        }
    }, []);

    const addressFields = useMemo(() => {
        return fields.map(field => {
            return (
                <Field
                    key={field.id}
                    fieldKey={getFieldKey(field.attribute_code)}
                    {...field}
                    initialValue={initialValues[field.attribute_code]}
                    validate={isFieldRequired}
                />
            );
        });
    }, [fields, initialValues, isFieldRequired]);

    const billingAddressFieldsClassName = isBillingAddressSame
        ? classes.billing_address_fields_root_hidden
        : classes.billing_address_fields_root;

    return (
        <div className={classes.root}>
            <ScrollAnchor ref={blockRef}>
                <FormError
                    classes={{ root: classes.formErrorContainer }}
                    errors={Array.from(errors.values())}
                />
                <div className={classes.address_check}>
                    <Checkbox
                        field="isBillingAddressSame"
                        label={formatMessage({
                            id: 'checkoutPage.billingAddressSame',
                            defaultMessage:
                                'Billing address same as shipping address'
                        })}
                        initialValue={initialValues.isBillingAddressSame}
                    />
                </div>
                <div className={billingAddressFieldsClassName}>
                    {addressFields}
                </div>
            </ScrollAnchor>
        </div>
    );
};

BillingAddress.propTypes = {
    classes: shape({ root: string }),
    shouldSubmit: bool.isRequired,
    onBillingAddressChangedError: func,
    onBillingAddressChangedSuccess: func
};

export default BillingAddress;
