import React from 'react';
import PropTypes, { string } from 'prop-types';
import { UNCONSTRAINED_SIZE_KEY } from '@magento/peregrine/lib/talons/Image/useImage';
import { Link } from 'react-router-dom';
import resourceUrl from '@magento/peregrine/lib/util/makeUrl';
import Image from '@magento/venia-ui/lib/components/Image';
import AddToCartButton from '@magento/venia-ui/lib/components/Gallery/addToCartButton';
import { useStyle } from '@magento/venia-ui/lib/classify';
import defaultClasses from './item.module.css';
import { FormattedMessage, useIntl } from 'react-intl';
import { useCompareContext } from '@tigrensolutions/compare/src/context';
import getProductUrl from '@tigrensolutions/base/helpers/getProductUrl';
import Price from '@magento/venia-ui/lib/components/Price';
import { useGalleryItem } from '@magento/peregrine/lib/talons/Gallery/useGalleryItem';
import WishlistGalleryButton from '@magento/venia-ui/lib/components/Wishlist/AddToListButton';

const IMAGE_WIDTH = 184;
const IMAGE_HEIGHT = 184;
const IMAGE_WIDTHS = new Map()
    .set(640, IMAGE_WIDTH)
    .set(UNCONSTRAINED_SIZE_KEY, 184);

const Item = props => {
    const { isPrint, product } = props;
    const {
        item,
        wishlistButtonProps,
        isSupportedProductType,
        itemRef,
        handleLinkClick
    } = useGalleryItem(props);
    const { formatMessage } = useIntl();
    const compareTalons = useCompareContext();
    const [
        { isRemoving, handleRemoveProduct, productUrlSuffix }
    ] = compareTalons;

    const classes = useStyle(defaultClasses, props.classes);

    if (!item) {
        return null;
    }

    const { name, small_image } = item;
    const productUrl = getProductUrl({
        product: item,
        url_suffix: productUrlSuffix
    });
    const productLink = resourceUrl(`/${productUrl}`);
    const rootClass = isRemoving ? classes.root_busy : classes.root;
    const addButton = isPrint ? null : isSupportedProductType ? (
        <AddToCartButton item={product} urlSuffix={productUrlSuffix} />
    ) : (
        <div className={classes.unavailableContainer}>
            <p>
                <FormattedMessage
                    id={'galleryItem.unavailableProduct'}
                    defaultMessage={'Currently unavailable for purchase.'}
                />
            </p>
        </div>
    );

    const wishlistButton =
        wishlistButtonProps && !isPrint ? (
            <WishlistGalleryButton
                {...wishlistButtonProps}
                buttonText={() =>
                    formatMessage({
                        id: 'AddToListButton.wishlist',
                        defaultMessage: 'Wishlist'
                    })
                }
            />
        ) : null;

    return (
        <div className={rootClass} ref={itemRef}>
            <div className={classes.topContent}>
                <Link
                    onClick={handleLinkClick}
                    to={productLink}
                    className={classes.images}
                >
                    <Image
                        alt={name}
                        classes={{
                            image: classes.image,
                            root: classes.imageContainer
                        }}
                        resource={small_image}
                        widths={IMAGE_WIDTHS}
                        height={IMAGE_HEIGHT}
                    />
                </Link>
                <Link
                    onClick={handleLinkClick}
                    to={productLink && resourceUrl(productLink)}
                    className={classes.name}
                >
                    <span dangerouslySetInnerHTML={{ __html: name }} />
                </Link>
            </div>
            <div className={classes.cartActions}>
                <div className={classes.addToCartAction}>
					<div className={classes.priceSection}>
                    <Price
                        value={
                            item?.price_range?.maximum_price?.regular_price
                                ?.value
                        }
                        currencyCode={
                            item?.price_range?.maximum_price?.regular_price
                                ?.currency
                        }
                        type={'full'}
                        classes={{ ...classes, root: classes.rootPrice }}
                        product={item}
                        onList={true}
                    />
					</div>
                    <div className={classes.wrapCart}>{addButton}</div>
                </div>
                <div className={classes.wrapBtn}>
                    <div className={classes.wishlist}>{wishlistButton}</div>
                    {!isPrint && (
                        <button
                            className={classes.wrapRemove}
                            onClick={() => handleRemoveProduct(item)}
                        >
                            <span className={classes.removeIcon} />
                            <span className={classes.textRemove}>
                                <FormattedMessage
                                    id="compare.remove"
                                    defaultMessage="Remove Product"
                                />
                            </span>
                        </button>
                    )}
                </div>
            </div>
        </div>
    );
};

Item.propTypes = {
    classes: PropTypes.shape({
        addToCartAction: string,
        productLink: string
    })
};

export default Item;
