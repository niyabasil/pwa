import React from 'react';
import { number } from 'prop-types';
import { useStyle } from '@magento/venia-ui/lib/classify';
import GalleryItemShimmer from '@magento/venia-ui/lib/components/Gallery/item.shimmer';
import defaultClasses from './gallerySlider.shimmer.module.css';
import SlickSlider from 'react-slick';

const GallerySliderShimmer = props => {
    const { slidesToShow } = props;
    const classes = useStyle(defaultClasses, props.classes);
    const content = new Array(slidesToShow)
        .fill(null)
        .map((item, index) => <GalleryItemShimmer key={index} />);

    const sliderSettings = {
        slidesToShow: slidesToShow,
        dots: false,
        arrows: false,
        lazyLoad: 'ondemand',
        infinite: false,
        responsive: [
            {
                breakpoint: 360,
                settings: {
                    slidesToShow: 1
                }
            },
            {
                breakpoint: 640,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 960,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 1280,
                settings: {
                    slidesToShow: 4
                }
            }
        ]
    };

    return (
        <div className={classes.rootSliderShimmer}>
            <SlickSlider {...sliderSettings}>{content}</SlickSlider>
        </div>
    );
};

GallerySliderShimmer.defaultProps = {
    slidesToShow: 4
};

GallerySliderShimmer.propTypes = {
    slidesToShow: number
};

export default GallerySliderShimmer;
