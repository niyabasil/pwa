export const CART_ADDRESS_INTERFACE_FIELDS = [
    'city',
    'company',
    'country',
    'country_id',
    'customer_notes',
    'firstname',
    'lastname',
    'postcode',
    'region',
    'street',
    'telephone'
];
export const FIELD_KEYS_FOR_ESTIMATE = [
    'country_code',
    'postcode',
    'city',
    'region_id',
    'street'
];

export const isCustomField = field =>
    !CART_ADDRESS_INTERFACE_FIELDS.includes(field);

export const getRegion = region => {
    if (!region) {
        return '';
    }
    return region.region_id || region.region || region.code || region.label;
};

export const formatAddress = formValues => {
    const {
        country,
        country_id,
        region,
        street,
        // eslint-disable-next-line no-unused-vars
        selectedPaymentMethod,
        // eslint-disable-next-line no-unused-vars
        isBillingAddressSame,
        // eslint-disable-next-line no-unused-vars
        __typename,
        // eslint-disable-next-line no-unused-vars
        agreements,
        ...address
    } = formValues;

    return {
        ...address,
        // Cleans up the street array when values are null or undefined
        street: street ? street.filter(e => e) : [''],
        country_code: country_id || country.code,
        region: getRegion(region)
    };
};

export const serializeCustomAttributes = customAttributes => {
    if (!Array.isArray(customAttributes) || !customAttributes.length) {
        return {};
    }

    const custom_attributes = customAttributes
        .filter(f => isCustomField(f.attribute_code))
        .reduce((acc, curr) => {
            if (curr.value && curr.value !== '-') {
                acc[curr.attribute_code] = curr.value;
            }
            return acc;
        }, {});

    return { custom_attributes };
};

export const deserializeCustomAttributes = serializedValue => {
    if (!serializedValue) {
        return [];
    }

    if (Array.isArray(serializedValue)) {
        return serializedValue
            .filter(f => isCustomField(f.attribute_code))
            .map(({ attribute_code, value }) => ({ attribute_code, value }));
    }

    return Object.entries(serializedValue).map(([key, value]) => ({
        attribute_code: key,
        value
    }));
};

export const getFieldKey = attributeCode => {
    const prefix = isCustomField(attributeCode) ? 'custom_attributes.' : '';
    return `${prefix}${attributeCode}`;
};

export const getRequiredFields = fields =>
    fields.reduce((res, field) => {
        if (Number(field.required)) {
            const fieldKey = getFieldKey(field.attribute_code);
            res.push(fieldKey);
        }

        return res;
    }, []);

export const customFormatsMap = {
    'yy-mm-dd': {
        label: 'yyyy-mm-dd',
        format: 'Y-m-d'
    },
    'mm/dd/yy': {
        label: 'mm/dd/yyyy',
        format: 'm/d/Y'
    },
    'dd/mm/yy': {
        label: 'dd/mm/yyyy',
        format: 'd/F/Y'
    },
    'd/m/yy': {
        label: 'd/m/yyyy',
        format: 'j/M/Y'
    },
    'dd.mm.yy': {
        label: 'dd.mm.yyyy',
        format: 'd.F.Y'
    },
    'dd.m.y': {
        label: 'dd.mm.yy',
        format: 'd.F.y'
    },
    'd.m.y': {
        label: 'd.m.yy',
        format: 'j.M.y'
    },
    'd.m.yy': {
        label: 'd.m.yyyy',
        format: 'j.M.Y'
    },
    'dd-mm-y': {
        label: 'dd-mm-yy',
        format: 'd-F-y'
    },
    'yy.mm.dd': {
        label: 'yyyy.mm.dd',
        format: 'Y.F.d'
    },
    'dd-mm-yy': {
        label: 'dd-mm-yyyy',
        format: 'd-M-Y'
    },
    'yy/mm/dd': {
        label: 'yyyy/mm/dd',
        format: 'Y/m/d'
    },
    'y/mm/dd': {
        label: 'yy/mm/dd',
        format: 'y/m/d'
    },
    'dd/mm/y': {
        label: 'dd/mm/yy',
        format: 'd/m/y'
    },
    'mm/dd/y': {
        label: 'mm/dd/yy',
        format: 'm/d/y'
    },
    'dd/mm yy': {
        label: 'dd/mm yyyy',
        format: 'd/m Y'
    },
    'yy mm dd': {
        label: 'yyyy mm dd',
        format: 'Y m d'
    }
};

export const phpToMoment = (str = '') => {
    const { format = 'm/d/Y' } = customFormatsMap[str.toLowerCase()] || {};

    const replacements = {
        d: 'DD',
        D: 'ddd',
        j: 'D',
        l: 'dddd',
        N: 'E',
        S: 'o',
        w: 'e',
        z: 'DDD',
        W: 'W',
        F: 'MMMM',
        m: 'MM',
        M: 'MMM',
        n: 'M',
        t: '', // no equivalent
        L: '', // no equivalent
        o: 'YYYY',
        Y: 'YYYY',
        y: 'YY',
        a: 'a',
        A: 'A',
        B: '', // no equivalent
        g: 'h',
        G: 'H',
        h: 'hh',
        H: 'HH',
        i: 'mm',
        s: 'ss',
        u: 'SSS',
        e: 'zz', // deprecated since Moment.js 1.6.0
        I: '', // no equivalent
        O: '', // no equivalent
        P: '', // no equivalent
        T: '', // no equivalent
        Z: '', // no equivalent
        c: '', // no equivalent
        r: '', // no equivalent
        U: 'X'
    };

    return format
        .split('')
        .map(chr => (chr in replacements ? replacements[chr] : chr))
        .join('');
};
