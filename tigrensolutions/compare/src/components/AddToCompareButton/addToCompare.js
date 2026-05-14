import React from 'react';
import { FormattedMessage } from 'react-intl';
import { useCompareContext } from '../../context';
import { BarChart } from 'react-feather';
import { useStyle } from '@magento/venia-ui/lib/classify';
import defaultClasses from './addToCompareButton.module.css';
import AddToListButton from '@magento/venia-ui/lib/components/Wishlist/AddToListButton/addToListButton.ce';
import Icon from '@magento/venia-ui/lib/components/Icon';

const HeartIcon = <Icon size={20} src={BarChart} />;

const AddToCompareButton = props => {
    const { product, isProductDetail, isDialogOpen, isWishlistPage } = props;
    const classes = useStyle(defaultClasses, props.classes);
    const [{ handleAddToCompare, compareItems }] = useCompareContext();

    const productId = product?.id;
    const isInCompare = compareItems?.some(
        compareItem => compareItem.uid === productId?.toString()
    );

    const classInCompare = isInCompare ? classes.inCompare : '';
    const classProductDetail = isProductDetail
        ? classes.compareDetail
        : classes.compare;
    const classInCompareOpen = isDialogOpen ? classes.inCompareOpen : '';

    const classInWishlist = isWishlistPage ? classes.inWishlist : '';

    return (
        <button
            className={`${classProductDetail} ${classInCompare} ${classInCompareOpen} ${classInWishlist}`}
            priority="low"
            type="button"
            data-name="add-compare"
            disabled={isInCompare}
            onClick={() =>
                handleAddToCompare({
                    product
                })
            }
        >
            <span className={classes.icon}>{props.icon}</span>
            <span className={classes.text}>
                <FormattedMessage
                    id={'galleryItem.compare'}
                    defaultMessage={'Compare'}
                />
            </span>
        </button>
    );
};

AddToCompareButton.defaultProps = {
    icon: HeartIcon
};

export default AddToCompareButton;
