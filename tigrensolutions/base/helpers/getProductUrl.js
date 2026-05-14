const getProductUrl = ({ product = [], url_suffix = '.html' }) => {
    const { url_key, url_rewrites } = product;
    let url = `${url_key}${url_suffix}`;
    if (url_rewrites?.length > 0) {
        url = url_rewrites[0]?.url;
    }
    return url;
};

export default getProductUrl;
