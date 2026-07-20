import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';

import defaultClasses from './giftMessage.module.css';
import Field from '@magento/venia-ui/lib/components/Field';
import TextInput from '@magento/venia-ui/lib/components/TextInput';
import { useIntl } from 'react-intl';
import TextArea from '@magento/venia-ui/lib/components/TextArea';
import { Text } from 'informed';

const GiftMessageSection = props => {
    const { productName, field } = props;
    const { formatMessage } = useIntl();
    const classes = useStyle(defaultClasses, props.classes);
    const fieldPreffix = field ? `${field}.` : '';

    const title = formatMessage(
        {
            id: 'amOsc.giftMessageSectionTitle',
            defaultMessage: 'Gift Message for {productName} (optional)'
        },
        { productName }
    );

    return (
        <div className={classes.root}>
            <h3 className={classes.sectionTitle}>{title}</h3>
            <Text type="hidden" field={`${fieldPreffix}item_id`} />

            <Field
                label={formatMessage({
                    id: 'amOsc.to',
                    defaultMessage: 'To'
                })}
            >
                <TextInput field={`${fieldPreffix}recipient`} />
            </Field>

            <Field
                label={formatMessage({
                    id: 'amOsc.From',
                    defaultMessage: 'From'
                })}
            >
                <TextInput field={`${fieldPreffix}sender`} />
            </Field>
            <Field
                label={formatMessage({
                    id: 'amOsc.message',
                    defaultMessage: 'Message'
                })}
                optional
            >
                <TextArea field={`${fieldPreffix}message`} />
            </Field>
        </div>
    );
};

GiftMessageSection.propTypes = {
    classes: shape({
        root: string
    })
};

GiftMessageSection.defaultProps = {
    field: '',
    productName: 'Whole Order'
};

export default GiftMessageSection;
