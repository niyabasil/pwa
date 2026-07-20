import { gql } from '@apollo/client';

const GET_OSC_CONFIG = gql`
    query storeConfigData {
        storeConfig {
            store_code
            id
            amasty_checkout_general_enabled
            default_display_currency_code
            amasty_checkout_general_title
            amasty_checkout_general_description
            amasty_checkout_general_allow_edit_options
            amasty_checkout_general_bundling
            amasty_checkout_options_guest_checkout
            amasty_checkout_options_display_billing_address_on
            amasty_checkout_options_enable_agreements
            amasty_checkout_design_checkout_design
            amasty_checkout_design_layout
            amasty_checkout_design_layout_modern
            amasty_checkout_design_place_button_layout
            amasty_checkout_design_display_shipping_address_in
            amasty_checkout_design_heading_color
            amasty_checkout_design_summary_color
            amasty_checkout_design_bg_color
            amasty_checkout_design_button_color
            amasty_checkout_design_header_footer
            amasty_checkout_additional_options_create_account
            amasty_checkout_additional_options_create_account_checked
            amasty_checkout_additional_options_discount
            amasty_checkout_additional_options_newsletter
            amasty_checkout_additional_options_newsletter_checked
            amasty_checkout_additional_options_display_agreements
            amasty_checkout_additional_options_comment
            amasty_checkout_default_values_shipping_method
            amasty_checkout_default_values_payment_method
            amasty_checkout_default_values_address_country_id
            amasty_checkout_default_values_address_region_id
            amasty_checkout_default_values_address_postcode
            amasty_checkout_default_values_address_city
            amasty_checkout_customer_address_street_lines
            amasty_checkout_customer_create_account_vat_frontend_visibility
            amasty_checkout_custom_blocks_top_block_id
            amasty_checkout_custom_blocks_bottom_block_id
            amasty_checkout_success_page_block_id
            amasty_checkout_delivery_date_enabled
            amasty_checkout_delivery_date_date_required
            amasty_checkout_delivery_date_delivery_comment_enable
            amasty_checkout_delivery_date_delivery_comment_default
            amasty_checkout_geolocation_ip_detection
            amasty_checkout_geolocation_google_address_suggestion
            amasty_checkout_geolocation_google_api_key
            amasty_checkout_sales_gift_options_allow_order
            amasty_checkout_sales_gift_options_allow_items
            amasty_checkout_gifts_gift_wrap
            amasty_checkout_gifts_gift_wrap_fee
            amasty_checkout_additional_options_automatically_login
            amasty_checkout_additional_configuration {
                amasty_checkout_design_font
                amasty_checkout_delivery_date_available_days
                amasty_checkout_delivery_date_available_hours
                amasty_checkout_layout_builder_frontend_layout_config
                amasty_checkout_delivery_date_format
            }
            amasty_checkout_manage_checkout_fields {
                amasty_checkout_checkout_fields
            }
        }
    }
`;

const GET_IS_OSC_ENABLED = gql`
    query amastyOscEnabled {
        storeConfig {
            store_code
            id
            amasty_checkout_general_enabled
            amasty_checkout_options_guest_checkout
        }
    }
`;

const GET_DEFAULT_IP_DATA = gql`
    query getDefaultIpData {
        getDefaultIpData {
            country_id
            region
            region_id
            city
            postcode
        }
    }
`;

const GET_REGION = gql`
    query getRegion($countryId: String) {
        country(id: $countryId) {
            id
            available_regions {
                id
                code
                name
            }
        }
    }
`;

export default {
    getOscConfigQuery: GET_OSC_CONFIG,
    getOscEnabledQuery: GET_IS_OSC_ENABLED,
    getDefaultIpDataQuery: GET_DEFAULT_IP_DATA,
    getRegionQuery: GET_REGION
};
