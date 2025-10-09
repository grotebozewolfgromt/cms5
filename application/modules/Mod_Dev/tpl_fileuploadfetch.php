<?php
/**
 * Overview module: sys_modules
 */
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_security.php');


?>

<form action="fileuploadfetch-doupload.php" method="post" enctype="multipart/form-data" id="frmUpload">

    <input type="file" name="flUploadFetch" id="flUploadFetch">


    <input type="submit" name="submit">
</form>

<script>

        const objForm = document.getElementById("frmUpload");
        const objFileElement = document.getElementById("flUploadFetch");

        objForm.addEventListener("submit", objEvent =>
        {
            objEvent.preventDefault();
            // const sUploadScript = "http://nasischijf/cms5/www/application/modules/Mod_Dev/fileuploadfetch-doupload.php";
            const sUploadScript = "fileuploadfetch-doupload.php";

            //create new form, add files from the fileElement
            const objFormData = new FormData();
            objFormData.append("flUploadFetch", objFileElement.files[0]);

            //send it to the upload script using fetch
            fetch(sUploadScript, 
            {           
                method: "post",
                body: objFormData,
                redirect: "error"
            })
            .then(objData => 
            {
                console.log('Success:', objData);
            })            
            .catch((objError) => 
            {
                alert('error occured');
                console.log('Error:', objError);
            });
            
        });
</script>
