import { useCallback, useEffect, useState } from 'react';
import { useUserContext } from '@magento/peregrine/lib/context/user';
import { useAmOscContext } from '../../context';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from './guestFields.gql';
import { useMutation, useLazyQuery } from '@apollo/client';
import { useFieldState, useFormState, useFormApi } from 'informed';
import { useSignIn } from '@magento/peregrine/lib/talons/SignIn/useSignIn';
import { GET_CART_DETAILS_QUERY } from '@magento/venia-ui/lib/components/SignIn/signIn.gql';
import { useCartContext } from '@magento/peregrine/lib/context/cart';

export const VIEWS = {
    SIGN_IN: 'SIGN_IN',
    REGISTER: 'REGISTER'
};

const FORM_KEY = 'GUEST_FIELDS';

export const useGuestFields = (props = {}) => {
    const { toggleActiveContent } = props;

    const [{ isSignedIn }] = useUserContext();
    const [
        { shouldSubmit, amasty_checkout_additional_options_create_account },
        {
            setIsUpdating,
            setSectionError,
            setSectionCompleted,
            setPassword,
            setIsGuestEmailAvailable
        }
    ] = useAmOscContext();

    const [view, setView] = useState(null);
    const [{ cartId }] = useCartContext();
    const { handleSubmit, isBusy, errors } = useSignIn({
        getCartDetailsQuery: GET_CART_DETAILS_QUERY
    });

    const fieldState = useFieldState('email');
    const formState = useFormState();
    const formApi = useFormApi();

    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const { checkEmailQuery, setGuestEmailMutation } = operations;

    const [
        setGuestEmailCall,
        { loading: setGuestEmailLoading, error: setGuestEmailError }
    ] = useMutation(setGuestEmailMutation);

    const [
        runCheckEmailQuery,
        {
            loading: checkEmailLoading,
            data: checkEmailData,
            error: checkEmailError
        }
    ] = useLazyQuery(checkEmailQuery, {
        fetchPolicy: 'cache-and-network',
        nextFetchPolicy: 'cache-first'
    });

    const setError = useCallback(error => setSectionError([FORM_KEY, error]), [
        setSectionError
    ]);

    const checkEmail = useCallback(async () => {
        const { value } = fieldState || {};

        try {
            if (value) {
                const email = value;
                await setGuestEmailCall({
                    variables: { cartId, email }
                });

                await runCheckEmailQuery({
                    variables: { email }
                });
            }
        } catch {
            return;
        }
    }, [runCheckEmailQuery, setGuestEmailCall, fieldState, cartId]);

    useEffect(() => {
        const { isEmailAvailable } = checkEmailData || {};
        const { is_email_available } = isEmailAvailable || {};

        let currentView = null;

        if (!checkEmailError && is_email_available === false) {
            currentView = VIEWS.SIGN_IN;
        } else if (
            is_email_available &&
            amasty_checkout_additional_options_create_account === 2
        ) {
            currentView = VIEWS.REGISTER;
        }

        if (isEmailAvailable) {
            setIsGuestEmailAvailable(is_email_available);
        }

        setView(currentView);
    }, [
        setIsGuestEmailAvailable,
        checkEmailData,
        checkEmailError,
        amasty_checkout_additional_options_create_account
    ]);

    const handleSignIn = useCallback(() => {
        const { values } = formState;

        if (!values.password) {
            return formApi.setError('password', {
                id: 'validation.isRequired',
                defaultMessage: 'Is required.'
            });
        }
        return handleSubmit(values);
    }, [formState, handleSubmit, formApi]);

    const handleForgotPassword = useCallback(() => {
        toggleActiveContent();
    }, [toggleActiveContent]);

    useEffect(() => {
        const error = checkEmailError || setGuestEmailError;
        const errorMessage = error && {
            defaultMessage: error.message,
            id: 'amOsc.emailValidation'
        };
        formApi.setError('email', errorMessage);
    }, [checkEmailError, setGuestEmailError, formApi]);

    useEffect(() => {
        if (shouldSubmit) {
            try {
                const { validate, getState } = formApi || {};

                validate();
                const { errors, values } = getState();
                const hasErrors = Object.keys(errors).length;

                if (!hasErrors) {
                    setPassword(values.password || '');
                    return setSectionCompleted(FORM_KEY);
                } else {
                    throw new Error('Errors in the address form');
                }
            } catch (e) {
                return setError(e);
            }
        }
    }, [shouldSubmit, formApi, setSectionCompleted, setError, setPassword]);

    const isLoading = checkEmailLoading || setGuestEmailLoading;

    useEffect(() => {
        setIsUpdating(isLoading);
    }, [isLoading, setIsUpdating]);

    return {
        view,
        isSignedIn,
        checkEmail,
        isLoading,
        handleSignIn,
        isBusy,
        errors,
        handleForgotPassword
    };
};
