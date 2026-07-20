import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { arrayOf, bool, func, shape, string } from 'prop-types';
import additionalClasses from './addressCard.css';
import defaultClasses from '@magento/venia-ui/lib/components/CheckoutPage/AddressBook/addressCard.module.css';
import { Edit2 as EditIcon } from 'react-feather';
import { useAddressCard } from '@magento/peregrine/lib/talons/CheckoutPage/AddressBook/useAddressCard';
import Icon from '@magento/venia-ui/lib/components/Icon';
import Button from '@magento/venia-ui/lib/components/Button';
import { FormattedMessage } from 'react-intl';

const AddressCard = props => {
    const { address, isSelected, onEdit, onSelection, onChangeAddress } = props;

    const talonProps = useAddressCard({
        address,
        onEdit,
        onSelection
    });

    const {
        handleClick,
        handleEditAddress,
        handleKeyPress,
        hasUpdate
    } = talonProps;

    const {
        city,
        country_code,
        default_shipping,
        firstname,
        lastname,
        postcode,
        region: { region },
        street,
        telephone
    } = address;

    const classes = useStyle(defaultClasses, additionalClasses, props.classes);

    const rootClass = isSelected
        ? hasUpdate
            ? classes.root_updated
            : classes.root_selected
        : classes.root;

    const editButton = isSelected ? (
        <button className={classes.editButton} onClick={handleEditAddress}>
            <Icon
                classes={{
                    icon: classes.editIcon
                }}
                size={16}
                src={EditIcon}
            />
        </button>
    ) : null;

    const nameString = `${firstname} ${lastname}`;
    const streetRows = street.map((row, index) => {
        return <span key={index}>{row}</span>;
    });

    const changeAddressButton = onChangeAddress ? (
        <Button type={'button'} onClick={onChangeAddress}>
            <Icon
                classes={{
                    icon: classes.editIcon
                }}
                size={16}
                src={EditIcon}
            />
            <FormattedMessage id={'amOsc.edit'} defaultMessage={'Edit'} />
        </Button>
    ) : null;

    const additionalAddressString = `${city}, ${region} ${postcode} ${country_code}`;

    const defaultBadge = default_shipping ? (
        <span className={classes.defaultBadge}>
            <FormattedMessage
                id={'addressCard.defaultText'}
                defaultMessage={'Default'}
            />
        </span>
    ) : null;

    return (
        <div
            className={rootClass}
            onClick={onSelection ? handleClick : undefined}
            onKeyPress={handleKeyPress}
            role="button"
            tabIndex="0"
        >
            {defaultBadge}
            {editButton}
            <span className={classes.name}>{nameString}</span>
            {streetRows}
            <span>{additionalAddressString}</span>

            <a className={classes.phone} href={`tel: ${telephone}`}>
                {telephone}
            </a>

            {changeAddressButton}
        </div>
    );
};

AddressCard.propTypes = {
    address: shape({
        city: string,
        country_code: string,
        default_shipping: bool,
        firstname: string,
        lastname: string,
        postcode: string,
        region: shape({
            region_code: string,
            region: string
        }),
        street: arrayOf(string)
    }).isRequired,
    classes: shape({
        root: string,
        root_selected: string,
        root_updated: string,
        editButton: string,
        editIcon: string,
        defaultBadge: string,
        name: string,
        address: string
    }),
    isSelected: bool,
    onEdit: func,
    onSelection: func
};

export default AddressCard;
