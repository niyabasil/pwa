import React, { Fragment, Suspense } from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string } from 'prop-types';
import { useProductListing } from '../../../talons/OrderSummary/useProductListing';
import { ChevronDown, ChevronUp } from 'react-feather';
import defaultClasses from './productListing.css';
import LoadingIndicator from '@magento/venia-ui/lib/components/LoadingIndicator';
import { FormattedMessage } from 'react-intl';
import Icon from '@magento/venia-ui/lib/components/Icon';
import Product from './product';

const EditModal = React.lazy(() =>
    import('@magento/venia-ui/lib/components/CartPage/ProductListing/EditModal')
);

const ProductListing = props => {
    const { classes: propClasses, fetchCartDetails, setIsCartUpdating } = props;

    const talonProps = useProductListing();

    const {
        activeEditItem,
        isLoading,
        items,
        setActiveEditItem,
        handleToggle,
        isExpanded
    } = talonProps;

    const classes = useStyle(defaultClasses, propClasses);

    if (isLoading) {
        return (
            <LoadingIndicator>
                <FormattedMessage
                    id={'amOsc.loadingCartItems'}
                    defaultMessage={'Fetching Cart Items...'}
                />
            </LoadingIndicator>
        );
    }

    if (!items.length) {
        return null;
    }

    const toggleIconSrc = isExpanded ? ChevronUp : ChevronDown;
    const contentToggleIcon = <Icon src={toggleIconSrc} size={24} />;

    const products = items.map(product => (
        <Product
            item={product}
            key={product.uid}
            setIsCartUpdating={setIsCartUpdating}
            setActiveEditItem={setActiveEditItem}
            fetchCartDetails={fetchCartDetails}
        />
    ));

    const productsClassName = isExpanded
        ? classes.productList_open
        : classes.productList_hidden;

    return (
        <Fragment>
            <div className={classes.root}>
                <div className={classes.heading}>
                    <button
                        className={classes.expand}
                        type="button"
                        onClick={handleToggle}
                    >
                        <FormattedMessage
                            id={'amOsc.cartItemsTitle'}
                            defaultMessage={'{count} Items in Cart'}
                            values={{ count: items.length }}
                        />

                        {contentToggleIcon}
                    </button>
                </div>

                <ul className={productsClassName}>{products}</ul>
            </div>
            <Suspense fallback={null}>
                <EditModal
                    item={activeEditItem}
                    setIsCartUpdating={setIsCartUpdating}
                    setActiveEditItem={setActiveEditItem}
                />
            </Suspense>
        </Fragment>
    );
};

ProductListing.propTypes = {
    classes: shape({
        root: string
    })
};

export default ProductListing;
