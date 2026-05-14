import React, { useEffect, useRef } from 'react';
import { mergeClasses } from '@magento/venia-ui/lib/classify';

import { usePaypalExpress } from '@tigrensolutions/paypal-express/src/talons/usePaypalExpress';
import defaultClasses from './paypalExpress.module.css';
import { fullPageLoadingIndicator } from '@magento/venia-ui/lib/components/LoadingIndicator';
import { useToasts } from '@magento/peregrine';
import { loadScript } from '@tigrensolutions/paypal-express/src/utils/loadScript';

const inlineStyle = {
    position: 'relative',
    zIndex: '1'
};

const paypalButtonStyle = {
    color: 'gold',
    label: 'paypal',
    layout: 'vertical',
    shape: 'rect',
    size: 'responsive'
};

const PaypalExpress = props => {
    const classes = mergeClasses(defaultClasses, props.classes);
    const loaded = useRef(false);
    const [, { addToast }] = useToasts();

    const {
        loading,
        paypal_env,
        paypal_merchant_id,
        errorMessage,
        onAuthorize,
        payment,
        onError,
        loadedScript,
        handleLoadScript
    } = usePaypalExpress({
        ...props
    });

    useEffect(() => {
        loadScript('https://www.paypalobjects.com/api/checkout.js', () => {
            handleLoadScript();
        });
    }, []);
    useEffect(() => {
        if (
            !paypal_merchant_id ||
            !loadedScript ||
            loaded.current ||
            typeof paypal === 'undefined'
        ) {
            return;
        }
        try {
            const paypalProps = {
                env: paypal_env,
                locale: 'en_US',
                style: paypalButtonStyle,
                commit: true,
                payment,
                onAuthorize,
                onError,
                client: {}
            };
            paypalProps['client'][paypal_env] = paypal_merchant_id;
            paypal.Button.render(paypalProps, '#paypal-express')
                .then(() => {
                    loaded.current = true;
                })
                .finally(() => {
                    console.log('done');
                });
        } catch (error) {
            console.log(error);
            addToast({
                type: 'error',
                message: 'Something wrong.Please try again',
                timeout: 7000
            });
        }
    }, [paypal_merchant_id, loadedScript]);

    return (
        <div className={classes.root}>
            {loading && fullPageLoadingIndicator}
            <div id="paypal-express" style={inlineStyle} />
            {errorMessage && (
                <div className={classes.error}>
                    <p>{errorMessage}</p>
                </div>
            )}
        </div>
    );
};

PaypalExpress.defaultProps = {};

export default PaypalExpress;
