import React, { useMemo } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { bool, number, shape, string } from 'prop-types';
import defaultClasses from './street.css';
import Field from '@magento/venia-ui/lib/components/Field';
import TextInput from '@magento/venia-ui/lib/components/TextInput';
import { useStreet } from '../../talons/Street/useStreet';
import AutoCompleteInput from './autoCompleteInput';

const Street = props => {
    const {
        id,
        label,
        linesCount,
        validate,
        initialValue,
        ...inputProps
    } = props;
    const {
        amLinesCount,
        isAutocompleteEnabled,
        apiKey,
        handlePlaceSelected
    } = useStreet();

    const classes = useStyle(defaultClasses, props.classes);

    const lines = useMemo(() => {
        const result = [];
        const count = amLinesCount || linesCount;

        const autoCompleteLine = (
            <div className={classes.line} key={`street_autocomplete`}>
                <AutoCompleteInput
                    validate={validate}
                    field={'street[0]'}
                    id={`street0`}
                    apiKey={apiKey}
                    initialValue={initialValue ? initialValue[0] : undefined}
                    handlePlaceSelected={handlePlaceSelected}
                />
            </div>
        );

        for (let i = 0; i < count; i++) {
            if (i === 0 && isAutocompleteEnabled) {
                result.push(autoCompleteLine);
            } else {
                result.push(
                    <div className={classes.line} key={`street${i}`}>
                        <TextInput
                            validate={i === 0 && validate}
                            field={`street[${i}]`}
                            id={`street${i}`}
                            initialValue={
                                initialValue ? initialValue[i] : undefined
                            }
                            {...inputProps}
                        />
                    </div>
                );
            }
        }

        return result;
    }, [
        amLinesCount,
        linesCount,
        validate,
        classes.line,
        inputProps,
        isAutocompleteEnabled,
        apiKey,
        handlePlaceSelected,
        initialValue
    ]);

    return (
        <Field id={id} label={label}>
            {lines}
        </Field>
    );
};

Street.propTypes = {
    fieldProps: shape({
        attribute_code: string,
        default_label: string,
        label: string
    }),
    optional: bool,
    linesCount: number,
    classes: shape({
        line: string
    })
};

Street.defaultProps = {
    linesCount: 2
};

export default Street;
