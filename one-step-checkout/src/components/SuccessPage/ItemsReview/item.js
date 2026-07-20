import React from 'react';
import { FormattedMessage } from 'react-intl';

import ProductOptions from '@magento/venia-ui/lib/components/LegacyMiniCart/productOptions';
import Image from '@magento/venia-ui/lib/components/Image';
import { useStyle } from '@magento/venia-ui/lib/classify';
import configuredVariant from '@magento/peregrine/lib/util/configuredVariant';

import defaultClasses from './item.module.css';
import GiftMessage from '../giftMessage';

const Item = props => {
    const {
        classes: propClasses,
        product,
        quantity,
        configurable_options,
        isHidden,
        configurableThumbnailSource,
        item_gift_message
    } = props;
    const classes = useStyle(defaultClasses, propClasses);
    const className = isHidden ? classes.root_hidden : classes.root;
    const configured_variant = configuredVariant(configurable_options, product);
    return (
        <div className={className}>
            <div className={classes.item}>
                <Image
                    alt={product.name}
                    classes={{ root: classes.thumbnail }}
                    width={100}
                    resource={
                        configurableThumbnailSource === 'itself' &&
                        configured_variant
                            ? configured_variant.thumbnail.url
                            : product.thumbnail.url
                    }
                />
                <span className={classes.name}>{product.name}</span>
                <ProductOptions
                    options={configurable_options}
                    classes={{
                        options: classes.options
                    }}
                />
                <span className={classes.quantity}>
                    <FormattedMessage
                        id={'checkoutPage.quantity'}
                        defaultMessage={'Qty :'}
                        values={{ quantity }}
                    />
                </span>
            </div>
            <GiftMessage
                data={item_gift_message}
                expandable
                classes={{
                    section: classes.giftMessage
                }}
            />
        </div>
    );
};

export default Item;
