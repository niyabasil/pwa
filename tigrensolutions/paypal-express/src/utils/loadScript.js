export const loadScript = (url, callback) => {
    const allScript = document.getElementsByTagName('script');
    let isPaypalScriptLoaded = false;

    for (const script of allScript) {
        if (script.src === url) {
            isPaypalScriptLoaded = true;
            break;
        }
    }

    if (!isPaypalScriptLoaded) {
        const script = document.createElement('script');
        script.type = 'text/javascript';
        if (script.readyState) {
            // only required for IE <9
            script.onreadystatechange = function() {
                if (
                    script.readyState === 'loaded' ||
                    script.readyState === 'complete'
                ) {
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else {
            //Others
            script.onload = function() {
                callback();
            };
        }
        script.src = url;
        document.getElementsByTagName('head')[0].appendChild(script);
    } else {
        callback();
    }
};
