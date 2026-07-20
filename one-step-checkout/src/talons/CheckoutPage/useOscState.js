import { useCallback, useMemo, useReducer } from 'react';

const initialState = {
    isDoneMap: new Map(),
    errors: new Map(),
    shouldSubmit: false,
    isUpdating: false,
    isPaymentDone: false,
    shouldPaymentSubmit: false,
    paymentError: null,
    password: '',
    isGuestEmailAvailable: true
};

const reducer = (state, action) => {
    const { payload, type } = action;
    switch (type) {
        case 'set is done': {
            const [key, value] = payload;
            const isDoneMap = new Map(state.isDoneMap);
            const errors = new Map(state.errors);
            isDoneMap.set(key, value);

            if (value) {
                errors.delete(key);
            }

            return {
                ...state,
                isDoneMap,
                errors,
                shouldSubmit: value && state.shouldSubmit
            };
        }

        case 'set is payment done': {
            const value = payload;

            return {
                ...state,
                isPaymentDone: value,
                paymentError: !value ? state.paymentError : null
            };
        }
        case 'set error': {
            const [key, value] = payload;

            if (key === 'PAYMENT') {
                return {
                    ...state,
                    isPaymentDone: false,
                    shouldSubmit: false,
                    shouldPaymentSubmit: false,
                    paymentError: value
                };
            }

            const errors = new Map(state.errors);
            const isDoneMap = new Map(state.isDoneMap);
            errors.set(key, value);
            isDoneMap.set(key, false);

            return {
                ...state,
                errors,
                isDoneMap,
                shouldSubmit: false,
                shouldPaymentSubmit: false
            };
        }
        case 'click': {
            return {
                ...state,
                shouldSubmit: true
            };
        }

        case 'set should payment submit': {
            return {
                ...state,
                shouldPaymentSubmit: true
            };
        }
        case 'reset click': {
            return {
                ...state,
                shouldSubmit: false,
                shouldPaymentSubmit: false
            };
        }
        case 'set updating': {
            return {
                ...state,
                isUpdating: !!payload
            };
        }

        case 'set password': {
            return {
                ...state,
                password: payload
            };
        }

        case 'set is guest email available': {
            return {
                ...state,
                isGuestEmailAvailable: payload
            };
        }
    }
};

export const useOscState = () => {
    const [state, dispatch] = useReducer(reducer, initialState);

    const setSectionIsDone = useCallback(
        payload => {
            dispatch({ payload, type: 'set is done' });
        },
        [dispatch]
    );

    const setSectionError = useCallback(
        payload => {
            dispatch({ payload, type: 'set error' });
        },
        [dispatch]
    );

    const resetShouldSubmit = useCallback(() => {
        dispatch({ type: 'reset click' });
    }, [dispatch]);

    const setSectionCompleted = useCallback(
        payload => setSectionIsDone([payload, true]),
        [setSectionIsDone]
    );

    const setIsPaymentDone = useCallback(
        payload => {
            dispatch({ payload, type: 'set is payment done' });
        },
        [dispatch]
    );

    const setShouldPaymentSubmit = useCallback(
        () => dispatch({ type: 'set should payment submit' }),
        [dispatch]
    );

    const resetSectionCompleted = useCallback(
        payload => {
            setSectionIsDone([payload, false]);
        },
        [setSectionIsDone]
    );

    const setIsUpdating = useCallback(
        payload => {
            dispatch({ payload, type: 'set updating' });
        },
        [dispatch]
    );

    const setPlaceOrderButtonClicked = useCallback(
        payload => {
            dispatch({ payload, type: 'click' });
        },
        [dispatch]
    );

    const setPassword = useCallback(
        payload => dispatch({ payload, type: 'set password' }),
        [dispatch]
    );

    const setIsGuestEmailAvailable = useCallback(
        payload => dispatch({ payload, type: 'set is guest email available' }),
        [dispatch]
    );

    const api = useMemo(
        () => ({
            setSectionError,
            setSectionIsDone,
            setIsUpdating,
            setPlaceOrderButtonClicked,
            setSectionCompleted,
            resetSectionCompleted,
            resetShouldSubmit,
            setIsPaymentDone,
            setShouldPaymentSubmit,
            setPassword,
            setIsGuestEmailAvailable
        }),
        [
            setSectionError,
            setSectionIsDone,
            setSectionCompleted,
            resetSectionCompleted,
            setIsUpdating,
            setPlaceOrderButtonClicked,
            resetShouldSubmit,
            setIsPaymentDone,
            setShouldPaymentSubmit,
            setPassword,
            setIsGuestEmailAvailable
        ]
    );

    return [state, api];
};
