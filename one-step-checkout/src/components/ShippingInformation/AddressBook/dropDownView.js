import React, { useMemo, Fragment } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { bool, shape, string } from 'prop-types';

import defaultClasses from './dropDownView.css';
import Select from '@magento/venia-ui/lib/components/Select';
import { Form } from 'informed';
import { FormattedMessage, useIntl } from 'react-intl';
import Button from '@magento/venia-ui/lib/components/Button';
import AddressCard from './addressCard';
import AddressForm from '../AddressForm';

const DropDownView = props => {
    const {
        customerAddresses,
        handleSelectAddress,
        isUpdating,
        selectedAddress,
        handleSuccess,
        handleCancel,
        isEditing,
        handleShowEditArea,
        handleChangeAddress,
        showNewAddressForm
    } = props;

    const { formatMessage } = useIntl();
    const classes = useStyle(defaultClasses, props.classes);

    const currentAddress = useMemo(
        () =>
            customerAddresses.find(({ id }) => id === Number(selectedAddress)),
        [customerAddresses, selectedAddress]
    );

    if (!customerAddresses) {
        return null;
    }

    const selectOptions = customerAddresses.map(
        ({
            id,
            firstname,
            lastname,
            city,
            region,
            postcode,
            country_code,
            street
        }) => {
            const additionalAddressString = `${city}, ${
                region.region
            } ${postcode} ${country_code}`;
            const streetRows = street.join(' ');
            const label = `${firstname} ${lastname}, ${streetRows}, ${additionalAddressString}`;
            return {
                value: id,
                label
            };
        }
    );

    const editButtons = !showNewAddressForm ? (
        <div className={classes.buttons}>
            <Button disabled={isUpdating} onClick={handleCancel} priority="low">
                <FormattedMessage
                    id={'global.cancelButton'}
                    defaultMessage={'Cancel'}
                />
            </Button>

            <Button disabled={isUpdating} type={'submit'}>
                <FormattedMessage
                    id={'global.updateButton'}
                    defaultMessage={'Update'}
                />
            </Button>
        </div>
    ) : null;

    const newAddressForm = showNewAddressForm ? (
        <AddressForm onSuccess={handleSuccess} onCancel={handleCancel} />
    ) : null;

    const content = isEditing ? (
        <Fragment>
            <Form
                onSubmit={({ addressId }) => handleSelectAddress(addressId)}
                initialValues={{ addressId: selectedAddress }}
            >
                <Select
                    field={'addressId'}
                    onChange={handleChangeAddress}
                    disabled={isUpdating}
                    items={[
                        ...selectOptions,
                        {
                            value: 'new',
                            label: formatMessage({
                                id: 'amOsc.newAddress',
                                defaultMessage: 'New Address'
                            })
                        }
                    ]}
                />

                {editButtons}
            </Form>

            {newAddressForm}
        </Fragment>
    ) : (
        currentAddress && (
            <AddressCard
                address={currentAddress}
                onChangeAddress={handleShowEditArea}
            />
        )
    );

    return <div className={classes.root}>{content}</div>;
};

DropDownView.propTypes = {
    isLoading: bool,
    isEditing: bool,
    classes: shape({
        root: string
    })
};

export default DropDownView;
