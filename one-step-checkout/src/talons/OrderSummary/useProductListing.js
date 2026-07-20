import { useCallback, useState } from 'react';
import { useProductListing as defaultUseProductListing } from '@magento/peregrine/lib/talons/CartPage/ProductListing/useProductListing';

export const useProductListing = () => {
    const defaultTalonProps = defaultUseProductListing();

    const [isExpanded, setExpanded] = useState(true);

    const handleToggle = useCallback(() => {
        setExpanded(!isExpanded);
    }, [isExpanded, setExpanded]);

    return {
        ...defaultTalonProps,
        isExpanded,
        handleToggle
    };
};
