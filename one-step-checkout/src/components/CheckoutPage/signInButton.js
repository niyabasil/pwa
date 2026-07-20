import React from 'react';
import { mergeClasses } from '@magento/venia-ui/lib/classify';
import { func, shape, string } from 'prop-types';
import defaultClasses from '@magento/venia-ui/lib/components/CheckoutPage/checkoutPage.module.css';

//import defaultClasses from './signInButton.css';
import { FormattedMessage } from 'react-intl';
import Button from '@magento/venia-ui/lib/components/Button';

const SignInButton = props => {
    const { toggleSignInContent } = props;
    const classes = mergeClasses(defaultClasses, props.classes);

    return (
        <div className={classes.signInContainer}>
            <span className={classes.signInLabel}>
                <FormattedMessage
                    id={'checkoutPage.signInLabel'}
                    defaultMessage={'Sign in for Express Checkout'}
                />
            </span>
            <Button
                className={classes.signInButton}
                onClick={toggleSignInContent}
                priority="normal"
            >
                <FormattedMessage
                    id={'checkoutPage.signInButton'}
                    defaultMessage={'Sign In'}
                />
            </Button>
        </div>
    );
};

SignInButton.propTypes = {
    toggleSignInContent: func,
    classes: shape({
        root: string
    })
};

export default SignInButton;
