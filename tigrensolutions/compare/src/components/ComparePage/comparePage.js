import React, { useMemo } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import defaultClasses from './comparePage.module.css';
import CompareList from './CompareList';
import { useIntl } from 'react-intl';
import { StoreTitle } from '@magento/venia-ui/lib/components/Head';
import { useCompareContext } from '../../context';

const ComparePage = props => {
    const classes = useStyle(defaultClasses, props.classes);
    const [{ showLoading, isLoading, compareItems }] = useCompareContext();
    const { formatMessage } = useIntl();
    const wrapItems =
        !!compareItems && compareItems.length > 0 ? classes.rootWrap : '';
    const content = useMemo(() => {
        return (
            <CompareList
                onClose={() => {}}
                showLoading={showLoading}
                compareItems={compareItems}
                isLoading={isLoading}
            />
        );
    }, [isLoading, showLoading, compareItems]);

    return (
        <div className={`${wrapItems} ${classes.root}`}>
            <StoreTitle>
                {formatMessage({
                    id: 'comparePage.head',
                    defaultMessage: 'comparison list'
                })}
            </StoreTitle>
            {content}
        </div>
    );
};
export default ComparePage;
