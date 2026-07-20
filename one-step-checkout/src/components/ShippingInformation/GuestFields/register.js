import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';
import defaultClasses from './guestFields.css';
import Password from '@magento/venia-ui/lib/components/Password';
import { useIntl, FormattedMessage } from 'react-intl';
import { Message } from '@magento/venia-ui/lib/components/Field';
import { validatePassword } from '@magento/venia-ui/lib/util/formValidators';

const validatePass = value => {
    return !value ? undefined : validatePassword(value);
};

const validateConfirmPass = (value, values) => {
    const message = {
        id: 'amOsc.confirmPassValidation',
        defaultMessage: 'Please enter the same value again.'
    };
    return value === values.password ? undefined : message;
};

const Register = props => {
    const { formatMessage } = useIntl();
    const classes = useStyle(defaultClasses, props.classes);

    return (
        <div className={classes.registerFields}>
            <Password
                fieldName="password"
                label={formatMessage({
                    id: 'signIn.passwordText',
                    defaultMessage: 'Password'
                })}
                autoComplete="current-password"
                isToggleButtonHidden={false}
                validate={validatePass}
                validateOnBlur
            />
            <Message>
                <FormattedMessage
                    id={'amOsc.passwordMessage'}
                    defaultMessage={
                        'To register an account simply add a password.'
                    }
                />
            </Message>

            <Password
                fieldName="passwordConfirm"
                label={formatMessage({
                    id: 'amOsc.passwordConfirm',
                    defaultMessage: 'Confirm Password'
                })}
                autoComplete="off"
                validateOnBlur
                isToggleButtonHidden={false}
                validate={validateConfirmPass}
            />
            <Message>
                <FormattedMessage
                    id={'amOsc.confirmPasswordMessage'}
                    defaultMessage={'Please confirm your password.'}
                />
            </Message>
        </div>
    );
};

Register.propTypes = {
    classes: shape({
        root: string
    })
};

export default Register;
