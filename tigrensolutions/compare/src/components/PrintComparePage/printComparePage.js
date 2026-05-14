import React, { PureComponent, useEffect, useMemo, useRef, useState } from 'react';
import { useReactToPrint } from 'react-to-print';
import { useApolloClient } from '@apollo/client';

import { useStyle } from '@magento/venia-ui/lib/classify';

import BrowserPersistence from '@magento/peregrine/lib/util/simplePersistence';
import { useHistory } from 'react-router-dom';

import CompareProduct from '../ComparePage/CompareProduct/list';

import defaultClasses from './printPage.module.css';

import Logo from '@tigrensolutions/base/src/components/Logo';
import {GET_LOGO} from "@tigrensolutions/base/src/components/Logo/logo.js"
import PropTypes from 'prop-types';

const PrintComparePage = props => {
    const { classes: propsClasses ,height, width  } = props;

    const classes = useStyle(defaultClasses, propsClasses);
    const componentRef = useRef();
    const storage = new BrowserPersistence();
    const history = useHistory();

    const apolloClient = useApolloClient();
    const logoData = apolloClient.readQuery({
        query: GET_LOGO
    });

    const isLoadedLogo =  logoData?.storeConfig?.header_logo_src
    const src = logoData?.storeConfig?.header_logo_src
        ? '/media/logo/' + logoData.storeConfig.header_logo_src
        : '';
    const alt = logoData?.storeConfig?.logo_alt ? logoData.storeConfig.logo_alt : '';
    const logo_height = logoData?.storeConfig?.logo_height
        ? logoData.storeConfig.logo_height
        : height;
    const logo_width = logoData?.storeConfig?.logo_width
        ? logoData.storeConfig.logo_width
        : width;

    const compareItems = storage.getItem('compareDataPrint');


    if (!compareItems && !isLoadedLogo) {
        return null;
    }

    // eslint-disable-next-line react-hooks/rules-of-hooks
    const handlePrint = useReactToPrint({
        content: () => componentRef.current,
        onBeforePrint: () => {},
        onAfterPrint: () => {
            history.push('/catalog/product_compare');
        }
    });

    // eslint-disable-next-line react-hooks/rules-of-hooks

    class ContentCompare extends PureComponent {
        render() {
            return (
                <div>
                    <div className={classes.header}>
                        <img
                            alt={alt}
                            src={src}
                            height={logo_height}
                            width={logo_width}
                            classes={{ image: classes.logo }}
                        />
                    </div>
                    <div className={classes.content}>
                        <CompareProduct data={compareItems} isPrint={true} />
                    </div>
                </div>
            );
        }
    }

    // eslint-disable-next-line react-hooks/rules-of-hooks
    const pageContents = useMemo(() => {
        return <ContentCompare ref={componentRef} />;
    }, [compareItems]);

    // eslint-disable-next-line react-hooks/rules-of-hooks
    useEffect(() => {
        if (isLoadedLogo  && compareItems) {
            handlePrint();
        }
    }, [isLoadedLogo, compareItems]);

    return pageContents;
};

PrintComparePage.propTypes = {
    classes: PropTypes.shape({
        logo: PropTypes.string
    }),
    height: PropTypes.number,
    width: PropTypes.number
};

PrintComparePage.defaultProps = {
    height: 52,
    width: 267
};

export default PrintComparePage;
