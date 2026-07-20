import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';
import defaultClasses from './successPage.module.css';
import { FormattedMessage } from 'react-intl';

const OrderComment = props => {
    const { data } = props;
    const classes = useStyle(defaultClasses, props.classes);

    return (
        <div className={classes.section}>
            <div className={classes.sectionTitle}>
                <FormattedMessage
                    id={'amOsc.comments'}
                    defaultMessage={'Comments:'}
                />
            </div>
            <div className={classes.sectionContent}>{data}</div>
        </div>
    );
};

OrderComment.propTypes = {
    orderComment: string,
    classes: shape({
        root: string
    })
};

export default OrderComment;
