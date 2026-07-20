import mergeOperations from '@magento/peregrine/lib/util/shallowMerge';
import DEFAULT_OPERATIONS from './cmsBlock.gql';
import { useQuery } from '@apollo/client';

export const useCmsBlock = (props = {}) => {
    const { id } = props;
    const operations = mergeOperations(DEFAULT_OPERATIONS, props.operations);

    const { getCmsBlockQuery } = operations;

    const { data } = useQuery(getCmsBlockQuery, {
        variables: { identifiers: [id] },
        fetchPolicy: 'cache-and-network',
        nextFetchPolicy: 'cache-first',
        skip: !id
    });

    return {
        items: data ? data.cmsBlocks.items : null
    };
};
