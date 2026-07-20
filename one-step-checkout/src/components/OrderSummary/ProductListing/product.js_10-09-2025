import React, { useMemo } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';
import { FormattedMessage, useIntl } from 'react-intl';
import { useProduct } from '../../../talons/OrderSummary/useProduct';
import resourceUrl from '@magento/peregrine/lib/util/makeUrl';
import { Link } from 'react-router-dom';
import Image from '@magento/venia-ui/lib/components/Image';
import ProductOptions from '@magento/venia-ui/lib/components/LegacyMiniCart/productOptions';
import Price from '@magento/venia-ui/lib/components/Price';
import Quantity from '@magento/venia-ui/lib/components/CartPage/ProductListing/quantity';
import Icon from '@magento/venia-ui/lib/components/Icon';
import {
    Edit2 as Edit2Icon,
    Trash2 as TrashIcon,
    ChevronUp,
    ChevronDown
} from 'react-feather';

import defaultClasses from '@magento/venia-ui/lib/components/CartPage/ProductListing/product.module.css';
import productClasses from './product.css';

const IMAGE_SIZE = 100;

const Product = props => {
    const { item } = props;

    const { formatMessage } = useIntl();
    const talonProps = useProduct(props);

    const {
        errorMessage,
        handleEditItem,
        handleRemoveFromCart,
        handleUpdateItemQuantity,
        isEditable,
        product,
        isProductUpdating,
        isOptionsExpanded,
        toggleOptions,
        isAllowEdit
    } = talonProps;

    const {
        currency,
        image,
        name,
        options,
        quantity,
        stockStatus,
        unitPrice,
        urlKey,
        urlSuffix
    } = product;

    const classes = useStyle(defaultClasses, productClasses, props.classes);

    const itemClassName = isProductUpdating
        ? classes.item_disabled
        : classes.item;

    const editText = formatMessage({
        id: 'amOsc.editItem',
        defaultMessage: 'Edit'
    });

    const editItemButton =
        isAllowEdit && isEditable ? (
            <button
                className={classes.editBtn}
                onClick={handleEditItem}
                title={editText}
            >
                <Icon classes={classes.icon} size={14} src={Edit2Icon} />
                <span className={classes.editText}>{editText}</span>
            </button>
        ) : null;

    const removeButton = isAllowEdit ? (
        <button
            className={classes.removeBtn}
            onClick={handleRemoveFromCart}
            title={formatMessage({
                id: 'product.removeFromCart',
                defaultMessage: 'Remove from cart'
            })}
        >
            <Icon classes={classes.icon} size={16} src={TrashIcon} />
        </button>
    ) : null;

    const qty = isAllowEdit ? (
        <Quantity
            itemId={item.id}
            initialValue={quantity}
            onChange={handleUpdateItemQuantity}
        />
    ) : (
        <FormattedMessage
            id={'product.qty'}
            defaultMessage={'Qty: {quantity}'}
            values={{ quantity }}
        />
    );

    const itemLink = useMemo(
        () => resourceUrl(`/${urlKey}${urlSuffix || ''}`),
        [urlKey, urlSuffix]
    );

    const stockStatusMessage =
        stockStatus === 'OUT_OF_STOCK'
            ? formatMessage({
                  id: 'product.outOfStock',
                  defaultMessage: 'Out-of-stock'
              })
            : '';

    const toggleIconSrc = isOptionsExpanded ? ChevronUp : ChevronDown;
    const optionsToggleIcon = <Icon src={toggleIconSrc} size={16} />;
    const optionListClassname = isOptionsExpanded
        ? classes.optionList
        : classes.optionList_hidden;

    const productOptions =
        options && options.length ? (
            <div className={classes.options}>
                <button
                    className={classes.optionsToggle}
                    onClick={toggleOptions}
                >
                    <FormattedMessage
                        id={'amOsc.optionsViewDetails'}
                        defaultMessage={'View Details'}
                    />
                    {optionsToggleIcon}
                </button>

                <div className={optionListClassname}>
                    <ProductOptions
                        options={options}
                        classes={{
                            options: classes.options,
                            optionLabel: classes.optionLabel
                        }}
                    />
                </div>
            </div>
        ) : null;

    return (
        <li className={classes.root}>
            <span className={classes.errorText}>{errorMessage}</span>
            <div className={itemClassName}>
                <Link to={itemLink} className={classes.imageContainer}>
                    <Image
                        alt={name}
                        classes={{
                            root: classes.imageRoot,
                            image: classes.image
                        }}
                        width={IMAGE_SIZE}
                        resource={image}
                    />
                </Link>

                {editItemButton}

                <div className={classes.details}>
                    <div className={classes.name}>
                        <Link to={itemLink}>{name}</Link>
                    </div>
                    {productOptions}
                    <span className={classes.price}>
                        <Price currencyCode={currency} value={unitPrice} />
                        <FormattedMessage
                            id={'product.price'}
                            defaultMessage={' ea.'}
                        />
                    </span>
                    <span className={classes.stockStatusMessage}>
                        {stockStatusMessage}
                    </span>
                    <div className={classes.quantity}>{qty}</div>
                </div>

                {removeButton}
            </div>
        </li>
    );
};

Product.propTypes = {
    classes: shape({
        root: string
    })
};

export default Product;
