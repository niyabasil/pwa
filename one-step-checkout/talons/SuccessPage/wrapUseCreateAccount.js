import { useHistory } from 'react-router-dom';
import { useCallback } from 'react';

const wrapUseCreateAccount = original => props => {
    const history = useHistory();

    const onSubmit = useCallback(() => {
        props.onSubmit();
        history.push('/account-information');
    }, [props, history]);

    const defaultReturnData = original({
        ...props,
        onSubmit
    });

    return {
        ...defaultReturnData
    };
};

export default wrapUseCreateAccount;
