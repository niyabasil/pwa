const ModuleTransformConfigParent = require('@magento/pwa-buildpack/lib/WebpackTools/ModuleTransformConfig');

class ModuleTransformConfig extends ModuleTransformConfigParent {
    /**
     * Prevent modules from transforming files from other modules.
     * Preserves encapsulation and maintainability.
     * Except: allow module transformation if there is defined a rewrite (talons only)
     * @private
     */
    _assertAllowedToTransform({ requestor, fileToTransform }) {
        if (this._validateTransform(requestor, fileToTransform)) {
            throw this._traceableError(
                `Invalid fileToTransform path "${fileToTransform}": Extensions are not allowed to provide fileToTransform paths outside their own codebase! This transform request from "${requestor}" must provide a path to one of its own modules, starting with "${requestor}".`
            );
        }
    }

    /**
     * Validate a input transformation
     * @param requestor
     * @param fileToTransform
     * @returns {boolean}
     * @private
     */
    _validateTransform(requestor) {
        if (!this._isLocal(requestor)) {
            return false;
        }

        if (!this._isBuiltin(requestor)) {
            return false;
        }

        return true;
    }
}

module.exports = ModuleTransformConfig;
