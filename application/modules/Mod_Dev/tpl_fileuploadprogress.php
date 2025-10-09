<?php
/**
 * Overview module: sys_modules
 */
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_security.php');


?>

<form action="fileuploadprogress-doupload.php" method="post" enctype="multipart/form-data" id="frmUpload">

    <input type="file" name="flUpload" id="flUpload">


    <input type="submit" name="submit">
</form>

<div class="fileupload-progressbar" id="fileupload-progressbar">
    <div class="fileupload-progressbar-fill">
        <span class="fileupload-progressbar-text">0%</span>
    </div>
</div>

<script>

        const objForm = document.getElementById("frmUpload");
        const objFileElement = document.getElementById("flUpload");
        const objProgressBarFill = document.querySelector("#fileupload-progressbar > .fileupload-progressbar-fill"); //select first class (.fileupload-progressbar-fill) within id #fileupload-progressbar
        const objProgressBarText = objProgressBarFill.querySelector(".fileupload-progressbar-text"); 

        console.log(objForm );

        objForm.addEventListener("submit", objSubmitEvent =>
        {
            objSubmitEvent.preventDefault();

            const objXHR = new XMLHttpRequest();
            const sUploadScript = "fileuploadprogress-doupload.php";

            objXHR.open("POST", sUploadScript);
            objXHR.upload.addEventListener("progress", objProgressEvent =>
            {
                let iPercent = 0;
                if (objProgressEvent.lengthComputable) //sometimes it's not computable 
                    iPercent = Math.ceil(objProgressEvent.loaded / objProgressEvent.total * 100);   

                //update the data
                objProgressBarFill.style.width = iPercent + "%";
                objProgressBarText.textContent = iPercent + "%";
            });

            objXHR.addEventListener("loadend", () => 
            {
                objProgressBarText.textContent = "done";
            });            
            objXHR.send(new FormData(objForm));

            
        });
</script>
