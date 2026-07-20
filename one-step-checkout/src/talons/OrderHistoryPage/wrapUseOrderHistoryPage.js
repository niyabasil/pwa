import AM_OPERATIONS from './orderHistoryPage.gql';

const wrapUseOrderHistoryPage = original => props => {
    const defaultReturnData = original({
        ...props,
        operations: AM_OPERATIONS
    });

    return {
        ...defaultReturnData
    };
};

export default wrapUseOrderHistoryPage;
