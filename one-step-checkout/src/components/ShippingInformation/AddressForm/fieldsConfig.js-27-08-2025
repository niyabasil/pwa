import Country from '@magento/venia-ui/lib/components/Country';
import Region from '@magento/venia-ui/lib/components/Region';
import Postcode from '@magento/venia-ui/lib/components/Postcode';
import Street from '../../Street';

export const fieldConfig = {
    country_id: {
        component: Country,
        field: 'country_id'
    },
    postcode: {
        component: Postcode
    },
    region: {
        component: Region,
        fieldInput: 'region[region]',
        fieldSelect: 'region[region_id]',
        optionValueKey: 'id',
        countryCodeField: 'country_id',
        forceRequired: true
    },
    street: {
        component: Street
    }
};
