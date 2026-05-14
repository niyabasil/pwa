import React from 'react';
import PropTypes from 'prop-types';
import { gql, useQuery } from '@apollo/client';
import { useStyle } from '@magento/venia-ui/lib/classify';

import Image from '@magento/venia-ui/lib/components/Image';
import Shimmer from '@magento/venia-ui/lib/components/Shimmer';
import defaultClasses from './logo.module.css';

/**
 * A component that renders a logo in the header.
 *
 * @typedef Logo
 * @kind functional component
 *
 * @param {props} props React component props
 *
 * @returns {React.Element} A React component that displays a logo.
 */
const Logo = props => {
    const { data, loading } = useQuery(GET_LOGO, {
        fetchPolicy: 'cache-and-network',
        nextFetchPolicy: 'cache-first'
    });

    const { height, width, isNormalImg } = props;
    const classes = useStyle(defaultClasses, props.classes);
    const src = data?.storeConfig?.header_logo_src
        ? '/media/logo/' + data.storeConfig.header_logo_src
        : '';

    const alt = data?.storeConfig?.logo_alt ? data.storeConfig.logo_alt : '';
    const logo_height = data?.storeConfig?.logo_height
        ? data.storeConfig.logo_height
        : height;
    const logo_width = data?.storeConfig?.logo_width
        ? data.storeConfig.logo_width
        : width;

    if (loading) {
        return <Shimmer width={'150px'} height={'40px'} />;
    }

    return (
        <>
            {isNormalImg ? (
                data && (
                    <img
                        alt={alt}
                        src={src}
                        height={logo_height}
                        width={logo_width}
                    />
                )
            ) : (
                <Image
                    alt={alt}
                    classes={{ image: classes.logo }}
                    height={logo_height}
                    resource={src}
                    type={'logo'}
                    title={alt}
                    width={logo_width}
                />
            )}
        </>
    );
};

/**
 * Props for {@link Logo}
 *
 * @typedef props
 *
 * @property {Object} classes An object containing the class names for the
 * Logo component.
 * @property {string} classes.logo classes for logo
 * @property {number} height the height of the logo.
 */
Logo.propTypes = {
    classes: PropTypes.shape({
        logo: PropTypes.string
    }),
    height: PropTypes.number,
    width: PropTypes.number
};

Logo.defaultProps = {
    height: 52,
    width: 267
};

export default Logo;

export const GET_LOGO = gql`
    query storeConfigData {
        storeConfig {
            id
            store_code
            header_logo_src
            logo_width
            logo_height
            logo_alt
        }
    }
`;
