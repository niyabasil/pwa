import React, { useMemo } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { bool, func, shape, string } from 'prop-types';
import defaultClasses from './addressForm.css';
import { useAddressForm } from '../../../talons/ShippingInformation/useAddressForm';
import Field from './field';
import Button from '@magento/venia-ui/lib/components/Button';
import { FormattedMessage, useIntl } from 'react-intl';
import Checkbox from '@magento/venia-ui/lib/components/Checkbox';
import { Form, Text as HiddenInput } from 'informed';
import { getFieldKey } from '../../../utils';

const AddressForm = props => {
    const {
        shippingData,
        onCancel,
        onSuccess,
        isGuestCheckout,
        optionsFormProps,
        asyncForm
    } = props;

    const talonProps = useAddressForm({ shippingData, onSuccess });

    const {
        handleSubmit,
        initialValues,
        isSaving,
        isUpdate,
        fields
    } = talonProps;

    const addressFields = useMemo(() => {
        return fields.map(field => {
            return (
                <Field
                    key={field.id}
                    fieldKey={getFieldKey(field.attribute_code)}
                    {...field}
                />
            );
        });
    }, [fields]);
    const { formatMessage } = useIntl();
    const classes = useStyle(defaultClasses, props.classes);

    const cancelButton =
        !asyncForm && (isUpdate || onCancel) ? (
            <Button disabled={isSaving} onClick={onCancel} priority="low">
                <FormattedMessage
                    id={'global.cancelButton'}
                    defaultMessage={'Cancel'}
                />
            </Button>
        ) : null;

    const submitButtonText = isUpdate
        ? formatMessage({
              id: 'global.updateButton',
              defaultMessage: 'Update'
          })
        : formatMessage({
              id: 'global.addButton',
              defaultMessage: 'Add'
          });

    const submitButtonProps = {
        disabled: isSaving,
        priority: isUpdate ? 'high' : 'normal',
        type: 'submit'
    };

    const buttonsContainer = !asyncForm ? (
        <div className={classes.buttons}>
            {cancelButton}
            <Button {...submitButtonProps}>{submitButtonText}</Button>
        </div>
    ) : null;

    const saveInAddressBookElement = !isGuestCheckout ? (
        <div className={classes.checkboxContainer}>
            <Checkbox
                id="save_in_address_book"
                field="save_in_address_book"
                initialValue={true}
                label={formatMessage({
                    id: 'amOsc.saveInAddressBook',
                    defaultMessage: 'Save in address book'
                })}
            />
        </div>
    ) : (
        <HiddenInput
            field="save_in_address_book"
            initialValue={true}
            type="hidden"
        />
    );

    const defaultShippingElement =
        !isGuestCheckout && !asyncForm ? (
            <div className={classes.checkboxContainer}>
                <Checkbox
                    id="default_shipping"
                    field="default_shipping"
                    label={formatMessage({
                        id: 'customerForm.defaultShipping',
                        defaultMessage: 'Make this my default address'
                    })}
                />
            </div>
        ) : null;

    return (
        <Form
            className={classes.root}
            onSubmit={handleSubmit}
            initialValues={initialValues}
            {...optionsFormProps}
            allowEmptyStrings
        >
            {addressFields}
            {asyncForm && saveInAddressBookElement}
            {defaultShippingElement}
            {buttonsContainer}
        </Form>
    );
};

AddressForm.propTypes = {
    onCancel: func,
    onSuccess: func,
    isGuestCheckout: bool,
    asyncForm: bool,
    classes: shape({
        root: string
    })
};

export default AddressForm;
