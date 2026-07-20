import { useCallback } from 'react';
import { useAmOscContext } from '../../context';
import { useFormApi } from 'informed';

export const useStreet = () => {
    const [
        {
            amasty_checkout_customer_address_street_lines: amLinesCount,
            amasty_checkout_geolocation_google_address_suggestion: isAutocompleteEnabled,
            amasty_checkout_geolocation_google_api_key: apiKey
        }
    ] = useAmOscContext();

    const formApi = useFormApi();

    const handlePlaceSelected = useCallback(
        place => {
            const { address_components } = place || {};

            if (address_components.length) {
                let address1 = '';
                let postcode = '';
                const values = formApi.getValues();

                for (const component of address_components) {
                    const componentType = component.types[0];

                    switch (componentType) {
                        case 'street_number': {
                            address1 = `${component.long_name} ${address1}`;
                            break;
                        }

                        case 'route': {
                            address1 += component.short_name;
                            break;
                        }

                        case 'postal_code': {
                            postcode = `${component.long_name}${postcode}`;
                            break;
                        }

                        case 'postal_code_suffix': {
                            postcode = `${postcode}-${component.long_name}`;
                            break;
                        }

                        case 'locality':
                        case 'postal_town':
                            values.city = component.long_name;
                            break;

                        case 'administrative_area_level_1':
                        case 'administrative_area_level_2': {
                            values.region = {
                                region: component.long_name
                            };
                            break;
                        }

                        case 'country':
                            values.country_id = component.short_name;
                            break;
                    }
                }

                const newAddress = {
                    ...values,
                    postcode,
                    street: [address1]
                };

                formApi.setValues(newAddress);
            }
        },
        [formApi]
    );

    return {
        amLinesCount,
        isAutocompleteEnabled: isAutocompleteEnabled && apiKey,
        apiKey,
        handlePlaceSelected
    };
};
