/**
 * Webpack loader for splicing source code as text, to do
 * simple and fast operations like prepending/appending.
 *
 * The power behind the "source" methods in [TargetableModule][].
 *
 * This is custom transform source of Tigren.
 */
const { inspect } = require('util');

class SpliceError extends Error {
    constructor(message, instruction) {
        super(
            `Invalid splice instruction:\n\n${inspect(instruction, {
                compact: false
            })}\n\n${message}`
        );
    }
}

function transformSource(content) {
    return this.query.reduce((source, instr) => {
        const nope = msg => {
            this.emitError(new SpliceError(msg, instr));
            return source;
        };

        const { match, replace = '' } = instr;

        const pos = source.indexOf(match);
        return pos === -1
            ? nope(`The text "${match}" was not found.`)
            : source.replace(match, replace);
    }, content);
}

module.exports = transformSource;
