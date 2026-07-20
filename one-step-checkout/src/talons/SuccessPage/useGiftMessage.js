import { useCallback, useState } from 'react';

export const useGiftMessage = props => {
    const { expandable } = props;
    const [isExpanded, setExpanded] = useState(!expandable);
    const toggleHandler = useCallback(() => setExpanded(state => !state), [
        setExpanded
    ]);

    return {
        isExpanded,
        toggleHandler
    };
};
