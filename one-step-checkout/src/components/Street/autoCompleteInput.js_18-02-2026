import React, { Fragment } from 'react';
import { func, node, shape, string } from 'prop-types';
import { useField } from 'informed';
import AutoComplete from 'react-google-autocomplete';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { FieldIcons, Message } from '@magento/venia-ui/lib/components/Field';
import defaultClasses from '@magento/venia-ui/lib/components/TextInput/textInput.module.css';

const AutoCompleteInput = props => {
    const {
        after,
        before,
        classes: propClasses,
        message,
        apiKey,
        handlePlaceSelected,
        ...restProps
    } = props;

    const { fieldState, fieldApi, render, ref, userProps } = useField({
        ...restProps
    });

    const { value } = fieldState;
    const { setValue, setTouched } = fieldApi;
    const { onChange, onBlur, ...rest } = userProps;

    const classes = useStyle(defaultClasses, propClasses);

    const inputClass = fieldState.error ? classes.input_error : classes.input;

    return render(
        <Fragment>
            <FieldIcons after={after} before={before}>
                <AutoComplete
                    ref={ref}
                    {...rest}
                    className={inputClass}
                    apiKey={apiKey}
                    value={value}
                    onChange={e => {
                        setValue(e.target.value);
                        if (onChange) {
                            onChange(e);
                        }
                    }}
                    onBlur={() => {
                        setTouched(true);
                        if (onBlur) {
                            onBlur(e);
                        }
                    }}
                    onPlaceSelected={handlePlaceSelected}
                    options={{
                        types: ['address']
                    }}
                />
            </FieldIcons>
            <Message fieldState={fieldState}>{message}</Message>
        </Fragment>
    );
};

export default AutoCompleteInput;

AutoCompleteInput.propTypes = {
    apiKey: string,
    handlePlaceSelected: func,
    after: node,
    before: node,
    classes: shape({
        input: string
    }),
    field: string.isRequired,
    message: node
};
