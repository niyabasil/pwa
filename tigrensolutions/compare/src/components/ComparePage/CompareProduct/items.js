import React, { Fragment } from 'react';
import PropTypes, { string } from 'prop-types';
import mapProduct from '@magento/venia-ui/lib/util/mapProduct';
import { useStyle } from '@magento/venia-ui/lib/classify';
import Item from './item';
import Attribute from './attributes';
import defaultClasses from './items.module.css';
import { useGallery } from '@magento/peregrine/lib/talons/Gallery/useGallery';

const LIST_ATTRIBUTE_NOT_SHOW = ['sku', 'short_description'];
const Items = props => {
    const { items, addToCart, isPrint } = props;
    const talonProps = useGallery();
    const { storeConfig } = talonProps;
    const classes = useStyle(defaultClasses, props.classes);
    // Combine attributes of all items.
    const attributeCompareMap = new Map();
    items &&
        items.forEach(item => {
            item.product &&
                item.product.attributes &&
                item.product.attributes.forEach(attribute => {
                    if (!LIST_ATTRIBUTE_NOT_SHOW.includes(attribute.code)) {
                        attributeCompareMap.set(attribute.code, attribute);
                    }
                });
        });

    return (
        <div className={classes.compareWrapper}>
            <div className={classes.tableCompareWrapper}>
                <table className={classes.tableCompare}>
                    <thead>
                        <tr>
                            <td />
                            {items &&
                                items.length > 0 &&
                                items.map(item => (
                                    <Fragment key={item.uid}>
                                        <td>
                                            <Item
                                                storeConfig={storeConfig}
                                                item={mapProduct(item.product)}
                                                product={item.product}
                                                addToCart={addToCart}
                                                isPrint={isPrint}
                                            />
                                        </td>
                                    </Fragment>
                                ))}
                        </tr>
                    </thead>
                    <tbody>
                        {[...attributeCompareMap.values()].map(
                            (attribute, index) => {
                                return (
                                    <tr key={index}>
                                        <td>
                                            <span>{attribute.label} :</span>
                                        </td>
                                        {items.map(item => (
                                            <Fragment key={item.uid}>
                                                <td className={classes.item}>
                                                    <Attribute
                                                        item={item.product}
                                                        attribute={attribute}
                                                    />
                                                </td>
                                            </Fragment>
                                        ))}
                                    </tr>
                                );
                            }
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

Items.propTypes = {
    classes: PropTypes.shape({
        compareWrapper: string
    })
};

export default Items;
