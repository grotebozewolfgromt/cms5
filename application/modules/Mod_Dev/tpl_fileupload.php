<?php
/**
 * Overview module: sys_modules
 */
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_security.php');


?>
<form action="fileupload-doupload.php" method="post" enctype="multipart/form-data">

    <div class="file-dropzone">
        <span class="file-dropzone-prompt"><?php echo transcms('file_dropzone_prompt', 'Drop file here<br>or<br>click to upload') ?></span>
        <!-- <div class="file-dropzone-thumbnail" data-label="myfile.txt"></div> -->
        <input type="file" name="flUpload" class="file-dropzone-input" multiple>
    </div>

    <input type="submit" name="submit">
</form>
