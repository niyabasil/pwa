import React, { createContext, useContext, useMemo } from 'react';
import DEFAULT_OPERATIONS from './talons/storeConfig.gql';
import { useQuery } from '@apollo/client';
import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import { BrowserPersistence } from '@magento/peregrine/lib/util';
import {
    deserializeCustomAttributes,
    FIELD_KEYS_FOR_ESTIMATE,
    getRequiredFields,
    getRegion
} from './utils';
import { useOscState } from './talons/CheckoutPage/useOscState';
import { useUserContext } from '@magento/peregrine/lib/context/user';
const storage = new BrowserPersistence();
const AmOscContext = createContext();
const { Provider } = AmOscContext;

export const EMPTY_VALUE = '-';

export const MOCKED_ADDRESS = {
    country_code: DEFAULT_COUNTRY_CODE || 'US',
    region_id: 1,
    postcode: EMPTY_VALUE,
    city: EMPTY_VALUE,
    firstname: EMPTY_VALUE,
    lastname: EMPTY_VALUE,
    street: [EMPTY_VALUE],
    telephone: EMPTY_VALUE
};

const AmOscContextProvider = props => {

    const { children, amAllowGuestCheckout } = props;

    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);
    const {
        getOscConfigQuery,
        getDefaultIpDataQuery,
        getRegionQuery
    } = operations;

    const [{ isSignedIn }] = useUserContext();
    const [state, api] = useOscState();

    const isDisabledCheckout = !isSignedIn && !amAllowGuestCheckout;

    const { data: storeConfigData, loading } = useQuery(getOscConfigQuery, {
        fetchPolicy: 'cache-and-network',
        nextFetchPolicy: 'cache-first',
        skip: isDisabledCheckout
    });

    const { storeConfig } = storeConfigData || {};
    const config = useMemo(() => storeConfig || {}, [storeConfig]);

    const {
        amasty_checkout_manage_checkout_fields,
        amasty_checkout_geolocation_ip_detection: isEnabledGeoIp
    } = config;

    const { data: defaultGeoIpData, loading: geoIpLoading } = useQuery(
        getDefaultIpDataQuery,
        {
            fetchPolicy: 'cache-and-network',
            nextFetchPolicy: 'cache-first',
            skip: !isEnabledGeoIp || isDisabledCheckout
        }
    );

    const checkoutFields = useMemo(() => {
        const { amasty_checkout_checkout_fields } =
            amasty_checkout_manage_checkout_fields || {};

        try {
            return Object.values(JSON.parse(amasty_checkout_checkout_fields))
                .filter(({ enabled }) => !!Number(enabled))
                .sort((a, b) =>
                    Number(a.sort_order) < Number(b.sort_order) ? -1 : 1
                );
        } catch {
            return [];
        }
    }, [amasty_checkout_manage_checkout_fields]);

    const shippingAddressForEstimate = useMemo(() => {
        const geoIpData =
            isEnabledGeoIp && defaultGeoIpData
                ? defaultGeoIpData.getDefaultIpData
                : null;

        const getValue = key => {
            if (geoIpData && geoIpData[key]) {
                return geoIpData[key];
            }

            return (
                config[`amasty_checkout_default_values_address_${key}`] ||
                MOCKED_ADDRESS[key] ||
                EMPTY_VALUE
            );
        };

        const requiredFields = getRequiredFields(checkoutFields);
        const address = [...requiredFields, ...FIELD_KEYS_FOR_ESTIMATE].reduce(
            (acc, curr) => {
                const [key, subkey] = curr.split('.');

                return {
                    ...acc,
                    [key]: !subkey
                        ? getValue(curr)
                        : {
                              ...acc[key],
                              [subkey]: getValue(subkey)
                          }
                };
            },
            {}
        );

        const { custom_attributes, country_id, region_id, ...rest } = address;

        return {
            ...rest,
            country_code:
                country_id && country_id !== EMPTY_VALUE
                    ? country_id
                    : MOCKED_ADDRESS.country_code,
            region: {
                region_id: !isNaN(Number(region_id))
                    ? region_id || MOCKED_ADDRESS.region
                    : undefined,
                region: region_id || MOCKED_ADDRESS.region
            },
            custom_attributes: deserializeCustomAttributes(custom_attributes)
        };
    }, [config, defaultGeoIpData, isEnabledGeoIp, checkoutFields]);

    const { data: regionData, loading: regionLoading } = useQuery(
        getRegionQuery,
        {
            variables: {
                countryId: shippingAddressForEstimate.country_code
            },
            skip: !storeConfigData
        }
    );

    const defaultShippingAddress = useMemo(() => {
        const regionValue = getRegion(shippingAddressForEstimate.region);

        if (!regionData) {
            return null;
        }

        if (
            shippingAddressForEstimate.country_code !==
                MOCKED_ADDRESS.country_code &&
            regionValue === MOCKED_ADDRESS.region_id
        ) {
            const { country } = regionData || {};
            const region = Array.isArray(country.available_regions)
                ? country.available_regions[0].id
                : EMPTY_VALUE;
            return {
                ...shippingAddressForEstimate,
                region: { region_id: region }
            };
        }

        return shippingAddressForEstimate;
    }, [regionData, shippingAddressForEstimate]);

    const contextValue = [
        {
            ...state,
            ...config,
            checkoutFields,
            cartCurrency:
                storage.getItem('store_view_currency') ||
                config.default_display_currency_code ||
                'USD',
            defaultShippingAddress,
            loading: loading || regionLoading || geoIpLoading,
            isDisabledCheckout
        },
        api
    ];

    return <Provider value={contextValue}>{children}</Provider>;
};

export default AmOscContextProvider;

export const useAmOscContext = () => useContext(AmOscContext);
