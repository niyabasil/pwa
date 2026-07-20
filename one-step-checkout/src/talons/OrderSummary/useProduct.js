import { useProduct as useCartProduct } from '@magento/peregrine/lib/talons/CartPage/ProductListing/useProduct';
import {
    REMOVE_ITEM_MUTATION,
    UPDATE_QUANTITY_MUTATION
} from '@magento/venia-ui/lib/components/CartPage/ProductListing/product';
import { useCallback, useState } from 'react';
import { useAmOscContext } from '../../context';

export const useProduct = props => {
    const talonProps = useCartProduct({
        operations: {
            removeItemMutation: REMOVE_ITEM_MUTATION,
            updateItemQuantityMutation: UPDATE_QUANTITY_MUTATION
        },
        ...props
    });

    const [
        { amasty_checkout_general_allow_edit_options: isAllowEdit }
    ] = useAmOscContext();

    const [isOptionsExpanded, setOptionsExpanded] = useState(false);
    const toggleOptions = useCallback(
        () => setOptionsExpanded(!isOptionsExpanded),
        [setOptionsExpanded, isOptionsExpanded]
    );

    return {
        ...talonProps,
        isOptionsExpanded,
        toggleOptions,
        isAllowEdit
    };
};
