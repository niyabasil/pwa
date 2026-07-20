import React, { useCallback } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { func, number, shape, string } from 'prop-types';
import defaultClasses from './agreements.module.css';
import Checkbox from '@magento/venia-ui/lib/components/Checkbox';
import { isRequired } from '@magento/venia-ui/lib/util/formValidators';
import LinkButton from '@magento/venia-ui/lib/components/LinkButton';

const isShowCheckbox = mode => mode === 'MANUAL';

const AgreementItem = props => {
    const {
        agreement_id,
        checkbox_text,
        mode,
        handleOpenModal,
        ...rest
    } = props;

    const classes = useStyle(defaultClasses, props.classes);

    const handleClick = useCallback(() => handleOpenModal(rest), [
        rest,
        handleOpenModal
    ]);

    const label = (
        <LinkButton classes={{ root: classes.link }} onClick={handleClick}>
            {checkbox_text}
        </LinkButton>
    );

    const checkbox = isShowCheckbox(mode) ? (
        <Checkbox
            field={`agreements.agreement_${agreement_id}`}
            label={label}
            validate={isRequired}
            validateOnChange
        />
    ) : (
        label
    );

    return <div className={classes.root}>{checkbox}</div>;
};

AgreementItem.propTypes = {
    agreement_id: number,
    checkbox_text: string,
    mode: string,
    handleClick: func,
    classes: shape({
        root: string
    })
};

AgreementItem.defaultProps = {
    mode: 'AUTO'
};

export default AgreementItem;
