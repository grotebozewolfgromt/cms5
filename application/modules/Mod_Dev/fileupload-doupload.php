<?php
/**
 * controller 
 */
  
      
    //session started in bootstrap
    include_once '../../bootstrap_admin_auth.php';
 
    

    $target_dir = "";
    $target_file = $target_dir . basename($_FILES["flUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
      $check = getimagesize($_FILES["flUpload"]["tmp_name"]);
      if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
      } else {
        echo "File is not an image.";
        $uploadOk = 0;
      }
    }

    if (move_uploaded_file($_FILES["flUpload"]["tmp_name"], $target_file))
    {
        echo "The file ". htmlspecialchars( basename( $_FILES["flUpload"]["name"])). " has been uploaded.";
    } else 
    {
        echo "Sorry, there was an error uploading your file.";
    }
        
    


    /*

    //===fill tabsheets array (only if you want tabsheets)
    $arrTabsheets = $objCurrentModule->getTabsheets(); 
    
    
    //============ RENDER de templates
 
    
    
    $sTitle = transm(CMS_CURRENTMODULE, CMS_CURRENTMODULE);
    $sHTMLTitle = $sTitle;
    $sHTMLMetaDescription = $sTitle;
    
    
    $sHTMLContentMain = renderTemplate('tpl_fileupload.php', get_defined_vars());

    echo renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withmenu.php', get_defined_vars());
*/
    
?>