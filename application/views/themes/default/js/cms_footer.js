<?php 
/**
 * cms_footer.js
 *
 * This Javascript file contains the javascript for the cms
 * that needs to be handled AFTER the page is loaded.
 * 
 * THIS IS NOT YOUR DEFAULT DUMP FOR JAVASCRIPT!
 * BY DEFAULT JAVASCRIPT CODE GOES HERE: cms_header.js
 * 
 *************************************************************************
 * WARNING:
 *************************************************************************
 * this file uses PHP so it can ONLY be used by including it with PHP!
 * 
 *************************************************************************
 * 
 * @author Dennis Renirie
 * 
 * 29 apr 2024 cms_footer.js created
 */
?>



//===== SCROLLABLE TABSHEETS =====/
<?php //tabsheet tutorials: https://www.youtube.com/watch?v=XO42nk5PnzI , https://www.youtube.com/watch?v=N9O9gr1nIoQ , https://www.youtube.com/watch?v=XhVel8ZkN88 ?>
const objTabs = document.querySelectorAll(".scrollable-tabs-container a");
const objRightArrow = document.querySelector(".scrollable-tabs-container .right-arrow svg");
const objLeftArrow = document.querySelector(".scrollable-tabs-container .left-arrow svg");

const objTabsList = document.querySelector(".scrollable-tabs-container ul");
const objLeftArrowContainer = document.querySelector(".scrollable-tabs-container .left-arrow");
const objRightArrowContainer = document.querySelector(".scrollable-tabs-container .right-arrow");


/**
 * init state left-right icons
 */
window.addEventListener("load", (objEvent) => 
{
    manageScrollbarIcons();
});

/**
 * when window resizes, so may change what needs to be scrolled
 */
window.addEventListener("resize", (objEvent) => 
{
    manageScrollbarIcons();
});

/**
 * when clicked tab must be selected
 */
const removeAllActiveTabsheetClasses = () => 
{
    objTabs.forEach(tab =>{
        tab.classList.remove("active");
    })
}

objTabs.forEach(objTab => 
{
    objTab.addEventListener("click", () => 
    {
        removeAllActiveTabsheetClasses();
        objTab.classList.add("active");
    })
})

/**
 * showing left and right arrow?
 */
const manageScrollbarIcons = () =>
{
    if (objTabsList.scrollLeft >= 20)
    {
        objLeftArrowContainer.classList.add("active");
    }
    else
    {
        objLeftArrowContainer.classList.remove("active");
    }

    let iMaxScrollValue = objTabsList.scrollWidth - objTabsList.clientWidth - 20;

    if (objTabsList.scrollLeft >= iMaxScrollValue)
    {
        objRightArrowContainer.classList.remove("active");
    }
    else
    {
        objRightArrowContainer.classList.add("active");
    }
}

objRightArrow.addEventListener("click", () => 
{
    objTabsList.scrollLeft += 150;
    manageScrollbarIcons();
})

objLeftArrow.addEventListener("click", () => 
{
    objTabsList.scrollLeft -= 150;
    manageScrollbarIcons();
})

objTabsList.addEventListener("scroll", manageScrollbarIcons);

let bDraggingTabsheets = false;

const drag = (e) => {
    if (!bDraggingTabsheets) return;
    objTabsList.classList.add("dragging");
    objTabsList.scrollLeft -= e.movementX;
}

objTabsList.addEventListener("mousedown", () => 
{
    bDraggingTabsheets = true;
});

objTabsList.addEventListener("mousemove", drag);

document.addEventListener("mouseup", () => 
{
    bDraggingTabsheets = false;
    objTabsList.classList.remove("dragging");
})


//END ===== SCROLLABLE TABSHEETS =====/







//===== FILE DRAG AND DROP =====/

<?php //drag-drop tutorial: https://www.youtube.com/watch?v=Wtrin7C4b7w&t=1214s ?>
const sMessageDragDropNumberOfFilesDragged = "<?php echo transcms('file_dropzone_numberoffilesdragged', 'files') ?>";

// for all .file-dropzone-input elements 
document.querySelectorAll(".file-dropzone-input").forEach(inputElement => 
{
    // look for element with class .file-dropzone 
    const objDropZoneElement = inputElement.closest(".file-dropzone")
    


    //if user clicked: show file dialog
    objDropZoneElement.addEventListener("click", objEvent => 
    {
        inputElement.click(); //trigger click of inputElement
    });

    //when user selected a file from filedialog
    inputElement.addEventListener("change", objEvent => 
    {
        if (inputElement.files.length) //if at least 1 file is selected
        {
            updateFileDragThumbnail(objDropZoneElement, inputElement.files[0], inputElement.files.length);
        }
    })



    // if file dragging, add class .file-dropzone-ondragover 
    objDropZoneElement.addEventListener("dragover", objEvent => 
    {
        objEvent.preventDefault();//prevent default behavior of browser opening a file when dropping

        objDropZoneElement.classList.add("file-dropzone-dragover");
    });

    // if file dragging-out of dropregion or cancel (like hitting escape), remove class .file-dropzone-ondragover
    ["dragleave", "dragend"].forEach(type => 
    {
        objDropZoneElement.addEventListener(type, objEvent =>
        {
            objDropZoneElement.classList.remove("file-dropzone-dragover");
        })
    })



    //handling the actual file drop
    objDropZoneElement.addEventListener("drop", objEvent =>
    {
        objEvent.preventDefault();//prevent default behavior of browser opening a file when dropping
        
        if (objEvent.dataTransfer.files.length)
        {
            inputElement.files = objEvent.dataTransfer.files; //copy the dragged file properties to the actual html upload element in the form
            updateFileDragThumbnail(objDropZoneElement, objEvent.dataTransfer.files[0], objEvent.dataTransfer.files.length);
        }

        //remove the file-dropzone-dragover class since the drag-over action is completed with a drop action
        objDropZoneElement.classList.remove("file-dropzone-dragover");
    })
})


//update thumbnail
function updateFileDragThumbnail(objDropZoneElement, objFile, iNumberOfFilesDragged)
{
    let objThumbnailElement = objDropZoneElement.querySelector(".file-dropzone-thumbnail");

    //if prompt text "drag here" exists, remove it
    if (objDropZoneElement.querySelector(".file-dropzone-prompt"))
    {
        objDropZoneElement.querySelector(".file-dropzone-prompt").remove();
    }

    //first time there is no thumbnail element, so let's create it
    if (!objThumbnailElement)
    {
        objThumbnailElement = document.createElement("div");
        objThumbnailElement.classList.add("file-dropzone-thumbnail");
        objDropZoneElement.appendChild(objThumbnailElement);
    }

    //update label with either filename or number of files
    if (iNumberOfFilesDragged == 1)
        objThumbnailElement.dataset.label = objFile.name;
    else
        objThumbnailElement.dataset.label = iNumberOfFilesDragged + " " + sMessageDragDropNumberOfFilesDragged;

    //show thumbnail for images
    if (objFile.type.startsWith("image/"))
    {
        const objReader = new FileReader();
        
        objReader.readAsDataURL(objFile);//read file as base64 data
        objReader.onload = () => //if reading file done
        {
            objThumbnailElement.style.backgroundImage = `url('${ objReader.result }')`; //need to be backticks `
        };
    }
    else
    {
        objThumbnailElement.style.backgroundImage = null; 
    }

}

//END =====  DRAG & DROP