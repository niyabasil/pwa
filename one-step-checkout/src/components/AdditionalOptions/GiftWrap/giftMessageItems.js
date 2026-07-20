import React, { Fragment } from 'react';
import { shape, string } from 'prop-types';
import { useArrayField } from 'informed';
import GiftMessageSection from './giftMessageSection';

const GiftMessageItems = props => {
    const { field } = props;
    const { fields } = useArrayField({ field });

    const sections = fields.map(({ field, key, initialValue }) => (
        <GiftMessageSection
            key={key}
            field={field}
            productName={initialValue.productName}
        />
    ));

    return <Fragment>{sections}</Fragment>;
};

GiftMessageItems.propTypes = {
    field: string,
    classes: shape({
        root: string
    })
};

GiftMessageItems.defaultProps = {
    field: 'messageForItems'
};

export default GiftMessageItems;
