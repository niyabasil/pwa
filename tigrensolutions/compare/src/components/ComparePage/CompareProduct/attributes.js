import React from 'react';
import PropTypes from 'prop-types';
import findIndex from 'lodash/findIndex';

import { useStyle } from '@magento/venia-ui/lib/classify';

import defaultClasses from './attributes.module.css';

const Attribute = props => {
    const classes = useStyle(defaultClasses, props.classes);

    const { attribute, item } = props;
    const index = findIndex(item.attributes, function(product_attribute) {
        return product_attribute.code === attribute.code;
    });

    if (index === -1) {
        return <>-</>;
    }

    return (
        <span
            className={classes.root}
            dangerouslySetInnerHTML={{
                __html: item.attributes[index].value
            }}
        />
    );
};

Attribute.propTypes = {
    classes: PropTypes.shape({})
};

export default Attribute;
