/**! compression tag for ftp-deployment */

/**
 * Ready function
 */
(function ($) {
    'use strict';

    // initialize editor
    $("#post_content").initSimpleEditor({
        focus: true
    });

    // initialize attachements
    $('#post_imageFile_file').initSimpleFileInput();

    // initialize validator
    $("form").initValidator({
        simpleEditor: true,
        focus: false
    });
}(jQuery));
