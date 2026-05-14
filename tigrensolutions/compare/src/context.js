import React, { createContext, useCallback, useContext } from 'react';
import { useCompare } from './talons';
import BrowserPersistence from '@magento/peregrine/lib/util/simplePersistence';
import { useEventListener } from '@magento/peregrine/lib/hooks/useEventListener';

const CompareContext = createContext();
const { Provider } = CompareContext;

const CompareProvider = props => {
    const { children } = props;

    const talonProps = useCompare();
    const [compareState] = talonProps;

    // Storage listener to force a state update if compareId changes from another browser tab.
    const storageListener = useCallback(() => {
        const storage = new BrowserPersistence();
        const currentCompareId = storage.getItem('compareId');
        const { compareId } = compareState;
        if (compareId && currentCompareId && compareId !== currentCompareId) {
            globalThis.location && globalThis.location.reload();
        }
    }, [compareState]);

    useEventListener(globalThis, 'storage', storageListener);

    return <Provider value={talonProps}>{children}</Provider>;
};

export default CompareProvider;

export const useCompareContext = () => useContext(CompareContext);
