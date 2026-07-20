import React, { Fragment } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { func, shape, string } from 'prop-types';
import defaultClasses from './guestFields.css';
import Password from '@magento/venia-ui/lib/components/Password';
import { Message } from '@magento/venia-ui/lib/components/Field';
import { FormattedMessage, useIntl } from 'react-intl';
import Button from '@magento/venia-ui/lib/components/Button';
import LinkButton from '@magento/venia-ui/lib/components/LinkButton';

const validatePass = () => undefined;

const SignIn = props => {
    const { handleSignIn, handleForgotPassword } = props;
    const { formatMessage } = useIntl();

    const classes = useStyle(defaultClasses, props.classes);

    return (
        <Fragment>
            <Password
                fieldName="password"
                label={formatMessage({
                    id: 'signIn.passwordText',
                    defaultMessage: 'Password'
                })}
                autoComplete="current-password"
                isToggleButtonHidden={false}
                validate={validatePass}
                validateOnChange
            />
            <Message>
                <FormattedMessage
                    id={'amOsc.passwordMessage'}
                    defaultMessage={
                        'You already have an account with us. Sign in or continue as guest.'
                    }
                />
            </Message>
            <div className={classes.loginButtons}>
                <Button priority="high" type="button" onClick={handleSignIn}>
                    <FormattedMessage
                        id={'signIn.signInText'}
                        defaultMessage={'Sign In'}
                    />
                </Button>
                <LinkButton
                    classes={{ root: classes.forgotPasswordButton }}
                    type="button"
                    onClick={handleForgotPassword}
                >
                    <FormattedMessage
                        id={'signIn.forgotPasswordText'}
                        defaultMessage={'Forgot Password?'}
                    />
                </LinkButton>
            </div>
        </Fragment>
    );
};

SignIn.propTypes = {
    handleSignIn: func,
    handleForgotPassword: func,
    classes: shape({
        root: string
    })
};

export default SignIn;
