import { gql } from '@apollo/client';

const GET_CHECKOUT_AGREEMENTS = gql`
    query getCheckoutAgreements {
        checkoutAgreements {
            agreement_id
            checkbox_text
            content
            content_height
            is_html
            mode
            name
        }
    }
`;

const SAVE_ADDITIONAL_FIELDS = gql`
    mutation saveAdditionalFields(
        $additionalFields: SaveAdditionalFieldsInput
    ) {
        saveAdditionalFields(input: $additionalFields) {
            response
        }
    }
`;

export default {
    getCheckoutAgreementsQuery: GET_CHECKOUT_AGREEMENTS,
    saveAdditionalFieldsMutation: SAVE_ADDITIONAL_FIELDS
};
