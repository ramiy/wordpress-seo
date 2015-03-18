/**
 * Created by danny on 3/16/15.
 */
//sourceFiles = grunt.option('sourcefiles') || 'class-';
module.exports = {
    // Specify some options, usually specific to each plugin.
    options: {
        // Specifies string to be inserted between concatenated files.
        separator: ''
    },
    dist: {
        src: ['<%= files.classes %>'],
        dest: 'dest/admin/classConcat.php'
    }
}