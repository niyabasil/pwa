import { useCallback, useEffect, useRef, useState } from 'react';
import { useAmOscContext } from '../../context';

const blocksWithoutHeading = ['summary'];

export const useCheckoutBlock = props => {
    const { block, formKeys } = props;
    const [isExpanded, setIsExpanded] = useState(true);
    const blockRef = useRef();

    const [
        { amasty_checkout_design_checkout_design: design, errors }
    ] = useAmOscContext();

    const scrollBlockIntoView = useCallback(() => {
        if (blockRef.current) {
            blockRef.current.scrollIntoView({
                behavior: 'smooth'
            });
        }
    }, [blockRef]);

    const handleToggle = useCallback(() => {
        if (design === 1 && !blocksWithoutHeading.includes(block)) {
            // 1 -> modern design
            setIsExpanded(!isExpanded);
        }
    }, [isExpanded, design, block]);

    useEffect(() => {
        if (errors.size) {
            const errorsKeys = Array.from(errors.keys());

            if (formKeys.includes(errorsKeys[0])) {
                scrollBlockIntoView();
            }
        }
    }, [errors, formKeys, scrollBlockIntoView]);

    return {
        isExpanded,
        handleToggle,
        isHeadingHidden: blocksWithoutHeading.includes(block),
        blockRef
    };
};
