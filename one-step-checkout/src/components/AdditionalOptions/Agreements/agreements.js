import React, { Fragment, Suspense } from 'react';
import { useAgreements } from '../../../talons/AdditionalOptions/useAgreements';
import AgreementItem from './agreementItem';

const AgreementModal = React.lazy(() => import('./agreementModal'));

const Agreements = () => {
    const {
        checkoutAgreements,
        handleOpenModal,
        activeItem,
        handleCloseModal
    } = useAgreements();

    const agreements =
        Array.isArray(checkoutAgreements) && checkoutAgreements.length
            ? checkoutAgreements.map(agreement => (
                  <AgreementItem
                      key={agreement.agreement_id}
                      {...agreement}
                      handleOpenModal={handleOpenModal}
                  />
              ))
            : null;

    return (
        <Fragment>
            {agreements}

            <Suspense fallback={null}>
                <AgreementModal
                    isOpen={!!activeItem}
                    closeModal={handleCloseModal}
                    activeItem={activeItem}
                />
            </Suspense>
        </Fragment>
    );
};

export default Agreements;
