import React from 'react';
import PropTypes from 'prop-types';
import { FormattedMessage } from 'react-intl';
import { useStyle } from '@magento/venia-ui/lib/classify';
import LoadingIndicator, {
    fullPageLoadingIndicator
} from '@magento/venia-ui/lib/components/LoadingIndicator';
import CompareProduct from '../CompareProduct';
import defaultClasses from './compareList.module.css';

const CompareList = props => {
    const classes = useStyle(defaultClasses, props.classes);

    const { showLoading, compareItems, isLoading } = props;

    if (isLoading) {
        return (
            <div className={classes.itemActionLoading}>
                <LoadingIndicator />
            </div>
        );
    }

    if (!compareItems || (compareItems && compareItems.length === 0))
        return (
            <div className={classes.empty}>
                <p>
                    <FormattedMessage
                        id="compare.empty"
                        defaultMessage="You have no items in your compare."
                    />
                </p>
            </div>
        );
    return (
        <>
            {showLoading && fullPageLoadingIndicator}
            <CompareProduct data={compareItems} />
        </>
    );
};

CompareList.propTypes = {
    classes: PropTypes.shape({})
};

export default CompareList;
