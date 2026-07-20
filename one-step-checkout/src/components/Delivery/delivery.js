import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';
import { Calendar as DateIcon } from 'react-feather';
import defaultClasses from './delivery.module.css';
import { useIntl } from 'react-intl';
import { Form } from 'informed';
import Field from '@magento/venia-ui/lib/components/Field';
import Icon from '@magento/venia-ui/lib/components/Icon';
import { isRequired } from '@magento/venia-ui/lib/util/formValidators';
import Datetime from 'react-datetime';
import 'react-datetime/css/react-datetime.css';
import Select from '@magento/venia-ui/lib/components/Select';
import TextArea from '@magento/venia-ui/lib/components/TextArea';
import TextInput from '@magento/venia-ui/lib/components/TextInput';
import { useDelivery } from '../../talons/Delivery/useDelivery';

const Delivery = props => {
    const talonProps = useDelivery(props);
    const { formatMessage } = useIntl();
    const classes = useStyle(defaultClasses, props.classes);

    const {
        isEnabled,
        isDateRequired,
        isCommentEnabled,
        commentPlaceholder,
        availableHours,
        validateDays,
        optionsFormProps,
        onDateChange
    } = talonProps;

    if (!isEnabled) {
        return null;
    }

    const comment = isCommentEnabled ? (
        <Field
            id="comment"
            label={formatMessage({
                id: 'amOsc.deliveryComment',
                defaultMessage: 'Delivery Comment'
            })}
            optional
        >
            <TextArea
                id="comment"
                field={'comment'}
                placeholder={commentPlaceholder}
            />
        </Field>
    ) : null;

    const icon = <Icon src={DateIcon} size={18} />;

    return (
        <Form className={classes.form} {...optionsFormProps}>
            <Field
                id="date"
                label={formatMessage({
                    id: 'amOsc.deliveryDate',
                    defaultMessage: 'Delivery Date'
                })}
                optional={!isDateRequired}
            >
                <Datetime
                    isValidDate={validateDays}
                    timeFormat={false}
                    closeOnSelect
                    onChange={onDateChange}
                    renderInput={({ value, onFocus }) => (
                        <TextInput
                            field={'date'}
                            id={'date'}
                            after={icon}
                            readOnly
                            onFocus={onFocus}
                            value={value}
                            validateOnChange
                            autoComplete="off"
                            validate={isDateRequired ? isRequired : undefined}
                        />
                    )}
                />
            </Field>

            <Field
                id="time"
                label={formatMessage({
                    id: 'amOsc.deliveryTime',
                    defaultMessage: 'Delivery Time Interval'
                })}
                optional
            >
                <Select id="time" field="time" items={availableHours} />
            </Field>

            {comment}
        </Form>
    );
};

Delivery.propTypes = {
    blockTitle: string,
    classes: shape({
        root: string
    })
};

export default Delivery;
