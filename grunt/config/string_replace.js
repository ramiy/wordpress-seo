/**
 * Created by danny on 3/16/15.
 */

module.exports = {

    fig: {
        src: 'admin/*.php',
        dist: 'dist/',
        dest: 'dest/',
        options: {
            replacements: [
                {
                    pattern: '<?php',
                    replacement: ' '
                },
                {
                    pattern: '<?',
                    replacement: ''
                }
            ]
        }
    }
}