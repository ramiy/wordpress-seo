/**
 * Created by danny on 3/16/15.
 */
module.exports = {
    default_options: {
        files: [
            {
                prepend: "<?php",
                append: "?>",
                input: "dest/admin/classConcat.php",
                output: "dest/admin/outputFile.php"
            }
        ]
    }
}