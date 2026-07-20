import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';
import Field from '@magento/venia-ui/lib/components/Field';
import defaultClasses from './field.css';
import TextInput from '@magento/venia-ui/lib/components/TextInput';
import { isRequired } from '@magento/venia-ui/lib/util/formValidators';
import { fieldConfig } from './fieldsConfig';

const FormField = props => {
    const {
        attribute_code,
        default_label,
        label,
        required,
        width,
        fieldKey,
        initialValue,
        customAttributeIdx,
        validate
    } = props;

    const classes = useStyle(defaultClasses, props.classes);

    const style = {
        '--amOscFieldWidth': `${width || 100}%`
    };

    const isOptional = !Number(required);
    const customField = fieldConfig[fieldKey];

    if (customField) {
        const {
            component: FieldComponent,
            forceRequired,
            ...rest
        } = customField;

        return (
            <div className={classes.root} style={style}>
                <FieldComponent
                    validate={
                        !isOptional || forceRequired
                            ? validate || isRequired
                            : undefined
                    }
                    id={attribute_code}
                    label={label || default_label}
                    idx={customAttributeIdx}
                    initialValue={initialValue}
                    validateOnBlur
                    validateOnChange
                    {...rest}
                />
            </div>
        );
    }

    return (
        <div className={classes.root} style={style}>
            <Field
                id={fieldKey}
                label={label || default_label}
                optional={isOptional}
            >
                <TextInput
                    field={fieldKey}
                    id={attribute_code}
                    validate={!isOptional ? validate || isRequired : undefined}
                    initialValue={initialValue}
                    validateOnBlur
                    validateOnChange
                />
            </Field>
        </div>
    );
};

FormField.propTypes = {
    attribute_code: string,
    attribute_id: string,
    default_label: string,
    enabled: string,
    label: string,
    required: string,
    width: string,
    classes: shape({
        root: string
    })
};

export default FormField;
