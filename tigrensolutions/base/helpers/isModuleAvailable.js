const fs = require('fs');

module.exports = function isModuleAvailable(moduleName) {
    let counter = 0;
    const dirSeparator = require('path').sep;

    for (const nodeModulesPath of module.paths) {
        const path = nodeModulesPath + dirSeparator + moduleName;

        const isExist = fs.existsSync(path);

        if (isExist) {
            return true;
        } else {
            counter++;
            if (counter === module.paths.length) return false;
        }
    }
};
