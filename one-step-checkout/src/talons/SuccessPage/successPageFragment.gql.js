import { gql } from '@apollo/client';
import { AddressFragment } from '../ShippingInformation/fragments.gql';
import { ItemsReviewFragment } from './itemsReviewFragments.gql';
import { DiscountSummaryFragment } from '@magento/peregrine/lib/talons/CartPage/PriceSummary/discountSummary.gql';
import { GiftCardSummaryFragment } from '@magento/peregrine/lib/talons/CartPage/PriceSummary/queries/giftCardSummary';
import { TaxSummaryFragment } from '@magento/peregrine/lib/talons/CartPage/PriceSummary/taxSummary.gql';
import { GrandTotalFragment } from '@magento/peregrine/lib/talons/CartPage/PriceSummary/priceSummaryFragments.gql';
import { GiftMessageFragment } from '../AdditionalOptions/GiftWrap/giftWrap.gql';

export const SuccessPageFragment = gql`
    fragment SuccessPageFragment on Cart {
        id
        email
        total_quantity
        shipping_addresses {
            ...AddressFragment
            selected_shipping_method {
                carrier_title
                method_title
                amount {
                    currency
                    value
                }
            }
        }
        billing_address {
            ...AddressFragment
        }
        selected_payment_method {
            code
            title
        }
        prices {
            ...TaxSummaryFragment
            ...DiscountSummaryFragment
            ...GrandTotalFragment
            subtotal_excluding_tax {
                currency
                value
            }
        }
        order_gift_message {
            ...GiftMessageFragment
        }
        ...GiftCardSummaryFragment
        ...ItemsReviewFragment
    }
    ${AddressFragment}
    ${ItemsReviewFragment}
    ${DiscountSummaryFragment}
    ${GiftCardSummaryFragment}
    ${GrandTotalFragment}
    ${TaxSummaryFragment}
    ${GiftMessageFragment}
`;
