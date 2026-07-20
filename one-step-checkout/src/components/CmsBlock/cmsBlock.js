import React from 'react';
import { useStyle } from '@magento/venia-ui/lib/classify';
import { shape, string, number } from 'prop-types';
import { useCmsBlock } from '../../talons/CmsBlock/useCmsBlock';
import defaultClasses from './cmsBlock.module.css';
import Block from '@magento/venia-ui/lib/components/CmsBlock/block';

const CmsBlock = props => {
    const { id } = props;
    const { items } = useCmsBlock({ id });
    const classes = useStyle(defaultClasses, props.classes);

    if (!id || !Array.isArray(items) || !items.length) {
        return null;
    }

    const blocks = items.map(item => (
        <Block key={item.identifier} className={classes.block} {...item} />
    ));

    return <div className={classes.root}>{blocks}</div>;
};

CmsBlock.propTypes = {
    id: number,
    classes: shape({
        root: string
    })
};

export default CmsBlock;
