import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { func, shape, string } from 'prop-types';
import defaultClasses from './guestFields.css';
import TextInput from '@magento/venia-ui/lib/components/TextInput';
import { isRequired } from '@magento/venia-ui/lib/util/formValidators';
import Field, { Message } from '@magento/venia-ui/lib/components/Field';
import { FormattedMessage, useIntl } from 'react-intl';
import {
    useGuestFields,
    VIEWS
} from '../../../talons/ShippingInformation/useGuestFields';
import LoadingIndicator from '@magento/venia-ui/lib/components/LoadingIndicator';
import FormError from '@magento/venia-ui/lib/components/FormError/formError';
import SignIn from './signIn';
import Register from './register';
import { Loader as LoaderIcon } from 'react-feather';
import Icon from '@magento/venia-ui/lib/components/Icon';

const GuestFields = props => {
    const { formatMessage } = useIntl();
    const talonProps = useGuestFields(props);

    const {
        isSignedIn,
        handleForgotPassword,
        checkEmail,
        view,
        checkEmailLoading,
        handleSignIn,
        isBusy,
        errors
    } = talonProps;

    const classes = useStyle(defaultClasses, props.classes);

    if (isSignedIn) {
        return null;
    }

    if (isBusy) {
        return (
            <div className={classes.root}>
                <LoadingIndicator>
                    <FormattedMessage
                        id={'signIn.loadingText'}
                        defaultMessage={'Signing In'}
                    />
                </LoadingIndicator>
            </div>
        );
    }

    const guestEmailMessage = !view ? (
        <Message>
            <FormattedMessage
                id={'amOsc.emailMessage'}
                defaultMessage={'You can create an account after checkout.'}
            />
        </Message>
    ) : null;

    let content = null;

    if (view === VIEWS.SIGN_IN) {
        content = (
            <SignIn
                handleForgotPassword={handleForgotPassword}
                handleSignIn={handleSignIn}
            />
        );
    } else if (view === VIEWS.REGISTER) {
        content = <Register />;
    }

    const loadingIcon = checkEmailLoading ? (
        <Icon
            size={16}
            src={LoaderIcon}
            classes={{ icon: classes.indicator }}
        />
    ) : null;

    return (
        <div className={classes.root}>
            <FormError errors={Array.from(errors.values())} />

            <Field
                id="email"
                label={formatMessage({
                    id: 'signIn.emailAddressText',
                    defaultMessage: 'Email address'
                })}
            >
                <TextInput
                    disabled={checkEmailLoading}
                    autoComplete="email"
                    field="email"
                    id="email"
                    type="email"
                    validate={isRequired}
                    onBlur={checkEmail}
                    after={loadingIcon}
                    validateOnBlur
                    validateOnChange
                />
                {guestEmailMessage}
            </Field>

            {content}
        </div>
    );
};

GuestFields.propTypes = {
    toggleActiveContent: func,
    classes: shape({
        root: string
    })
};

export default GuestFields;
