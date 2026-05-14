/**
 * @fileoverview This file houses functions that can be used for
 * validation of form fields.
 *
 * Note that these functions should return a string error message
 * when they fail, and `undefined` when they pass.
 */

const SUCCESS = undefined;

export const hasLengthAtLeast = (value, values, minimumLength) => {
    const message = {
        id: 'validation.hasLengthAtLeast',
        defaultMessage: 'Must contain more characters',
        value: minimumLength
    };
    if (!value || value.length < minimumLength) {
        return message;
    }

    return SUCCESS;
};

export const hasLengthAtMost = (value, values, maximumLength) => {
    if (value && value.length > maximumLength) {
        const message = {
            id: 'validation.hasLengthAtMost',
            defaultMessage: 'Must have less characters',
            value: maximumLength
        };
        return message;
    }

    return SUCCESS;
};

export const hasLengthExactly = (value, values, length) => {
    if (value && value.length !== length) {
        const message = {
            id: 'validation.hasLengthExactly',
            defaultMessage: 'Does not have exact number of characters',
            value: length
        };
        return message;
    }

    return SUCCESS;
};

/**
 * isRequired is provided here for convenience but it is inherently ambiguous and therefore we don't recommend using it.
 * Consider using more specific validators such as `hasLengthAtLeast` or `mustBeChecked`.
 */
export const isRequired = value => {
    const FAILURE = {
        id: 'validation.isRequired',
        defaultMessage: 'Is required.'
    };

    // The field must have a value (no null or undefined) and
    // if it's a boolean, it must be `true`.
    if (!value) return FAILURE;

    // If it is a number or string, it must have at least one character of input (after trim).
    const stringValue = String(value).trim();
    const measureResult = hasLengthAtLeast(stringValue, null, 1);

    if (measureResult) return FAILURE;
    return SUCCESS;
};

export const validatePassword = value => {
    const count = {
        lower: 0,
        upper: 0,
        digit: 0,
        special: 0
    };

    for (const char of value) {
        if (/[a-z]/.test(char)) count.lower++;
        else if (/[A-Z]/.test(char)) count.upper++;
        else if (/\d/.test(char)) count.digit++;
        else if (/\S/.test(char)) count.special++;
    }

    if (Object.values(count).filter(Boolean).length < 3) {
        const message = {
            id: 'validation.validatePassword',
            defaultMessage:
                'Minimum of different classes of characters in password is 3. Classes of characters: Lower Case, Upper Case, Digits, Special Characters.'
        };
        return message;
    }

    return SUCCESS;
};

export const validateConfirmPasswordCreate = (
    value,
    values,
    fieldKey = 'password'
) => {
    return value === values[fieldKey]
        ? SUCCESS
        : {
              id: 'validation.passwordNotMatch',
              defaultMessage: 'Please enter the same value again.'
          };
};
export const validatePhoneNumber = value => {
    const regex = /^([0-9])(\d){9}$/;
    if (regex.test(value)) {
        return SUCCESS;
    } else {
        return {
            id: 'validation.phoneNumber',
            defaultMessage: 'Invalid mobile number.'
        };
    }
};

export const validateEmail = value => {
    const regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    const enteredValue = regex.test(value);
    if (enteredValue) {
        return SUCCESS;
    } else {
        return {
            id: 'validation.email',
            defaultMessage:
                'Please enter a valid email address (Ex johndoe@domain.com).'
        };
    }
};

export const validateConfirmPassword = (
    value,
    values,
    fieldKey = 'newPassword'
) => {
    return value === values[fieldKey]
        ? SUCCESS
        : {
              id: 'validation.passwordNotMatch',
              defaultMessage: 'Password must match.'
          };
};
