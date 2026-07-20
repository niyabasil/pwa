import { gql } from '@apollo/client';
import { PriceSummaryFragment } from '@magento/peregrine/lib/talons/CartPage/PriceSummary/priceSummaryFragments.gql';
import { GiftWrapInformationFragment } from '../AdditionalOptions/GiftWrap/giftWrap.gql';

const GET_PRICE_SUMMARY = gql`
    query getPriceSummary($cartId: String!) {
        cart(cart_id: $cartId) {
            id
            ...PriceSummaryFragment
            ...GiftWrapInformationFragment
        }
    }
    ${PriceSummaryFragment}
    ${GiftWrapInformationFragment}
`;

export default {
    getPriceSummaryQuery: GET_PRICE_SUMMARY
};
