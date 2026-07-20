import { useAmOscContext } from '../../context';
import { useCallback, useMemo } from 'react';

const designConfig = {
    0: 'classic',
    1: 'modern'
};

export const useCheckoutContent = () => {
    const [
        {
            amasty_checkout_design_checkout_design: designKey,
            amasty_checkout_additional_configuration: {
                amasty_checkout_layout_builder_frontend_layout_config
            },
            ...config
        }
    ] = useAmOscContext();

    const [layout, classNames, design] = useMemo(() => {
        const layoutConfig = JSON.parse(
            amasty_checkout_layout_builder_frontend_layout_config
        );

        const designValue = designConfig[designKey];

        const classNameKeys = [designValue, `columns-${layoutConfig.length}`];

        return [layoutConfig, classNameKeys, designValue];
    }, [amasty_checkout_layout_builder_frontend_layout_config, designKey]);

    const isBlockEnabled = useCallback(
        section => {
            const { isEnabledOption } = section;
            return !isEnabledOption || config[isEnabledOption];
        },
        [config]
    );

    return {
        layout,
        classNames,
        design,
        isBlockEnabled
    };
};
