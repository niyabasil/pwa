import React, { useMemo } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string, array, func, number, bool } from 'prop-types';
import defaultClasses from './gridView.css';
import AddressCard from './addressCard';
import Button from '@magento/venia-ui/lib/components/Button';
import { FormattedMessage } from 'react-intl';

const GridView = props => {
    const {
        customerAddresses,
        handleAddAddress,
        handleEditAddress,
        handleSelectAddress,
        selectedAddress,
        isUpdating
    } = props;

    const classes = useStyle(defaultClasses, props.classes);

    const addressElements = useMemo(() => {
        let defaultIndex;
        const addresses = customerAddresses.map((address, index) => {
            const isSelected = selectedAddress === address.id;

            if (address.default_shipping) {
                defaultIndex = index;
            }

            return (
                <AddressCard
                    address={address}
                    isSelected={isSelected}
                    key={address.id}
                    onSelection={handleSelectAddress}
                    onEdit={handleEditAddress}
                />
            );
        });

        // Position the default address first in the elements list
        if (defaultIndex) {
            [addresses[0], addresses[defaultIndex]] = [
                addresses[defaultIndex],
                addresses[0]
            ];
        }

        return [...addresses];
    }, [
        customerAddresses,
        handleEditAddress,
        handleSelectAddress,
        selectedAddress
    ]);

    const rootClassName = isUpdating ? classes.root_updated : classes.root;

    return (
        <div className={rootClassName}>
            <div className={classes.content}>{addressElements}</div>
            <div className={classes.btn}>
                <Button type={'button'} onClick={handleAddAddress}>
                    <FormattedMessage
                        id={'amOsc.addNewAddresstext'}
                        defaultMessage={'New Address'}
                    />
                </Button>
            </div>
        </div>
    );
};

GridView.propTypes = {
    customerAddresses: array,
    handleAddAddress: func,
    handleEditAddress: func,
    handleSelectAddress: func,
    selectedAddress: number,
    isLoading: bool,
    classes: shape({
        root: string
    })
};

export default GridView;
