import { gql } from '@apollo/client';

export const GET_CMS_BLOCKS = gql`
    query cmsBlocks($identifiers: [String]!) {
        cmsBlocks(identifiers: $identifiers) {
            items {
                content
                identifier
            }
        }
    }
`;

export default {
    getCmsBlockQuery: GET_CMS_BLOCKS
};
