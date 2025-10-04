

/**
 * parent class for all grid based Design Objects
 * 2colums, 3 columns etc
 */
class DOGrid extends DesignObject
{
    sTitle = "<?php echo transm($sModule, 'pagebuilder_designobject_layouts_grid_title', 'Grid') ?>"; //language aware title shown to user
    sIconSVG = '<svg class="iconchangefill" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M10,14 L14,14 L14,10 L10,10 L10,14 Z M8,14 L8,10 L4,10 L4,14 L8,14 Z M10,4 L10,8 L14,8 L14,4 L10,4 Z M8,4 L4,4 L4,8 L8,8 L8,4 Z M10,20 L14,20 L14,16 L10,16 L10,20 Z M8,20 L8,16 L4,16 L4,20 L8,20 Z M16,14 L20,14 L20,10 L16,10 L16,14 Z M16,4 L16,8 L20,8 L20,4 L16,4 Z M16,20 L20,20 L20,16 L16,16 L16,20 Z M4,2 L20,2 C21.1045695,2 22,2.8954305 22,4 L22,20 C22,21.1045695 21.1045695,22 20,22 L4,22 C2.8954305,22 2,21.1045695 2,20 L2,4 C2,2.8954305 2.8954305,2 4,2 Z" fill-rule="evenodd"/></svg>';
    sSearchLabelsCSV = "p,<p>,<?php echo transm($sModule, 'pagebuilder_designobject_layouts_grid_searchlabelscsv', 'grid,table,columns'); ?>";
    sType = objDOTypes.layout; //type
    arrCategories = [objDOCategories.all, objDOCategories.layouts]; //category

    iInitCols = 3; //how many columns to generate on render()
    iInitRows = 3; //how many rows to generate on render()
    sInitGap = "10px";

    /**
     * renders element in designer
     */
    renderDesigner()
    {
        let objContainer = new DOContainer();
        let objDivGrid = document.createElement("div");
        let iInitCells = this.iInitCols * this.iInitRows;

        objDivGrid.style.display = "grid";
        objDivGrid.style.gridTemplateColumns = this.getGridTemplateColumnsText(this.iInitCols);
        objDivGrid.style.gap = this.sInitGap;

        //creating the "cells"
        objContainer.renderDesigner();
        for (let iCounter = 0; iCounter < iInitCells; iCounter++)
            objDivGrid.appendChild(objContainer.cloneNode(true));

        this.appendChild(objDivGrid);        
    }
    
    /**
     * render the detail tab
     * 
     * OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
     * 
     * @param {HTMLElement} objTabGridContainer --> container <div> of detailtab
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     */
    renderElementTab(objTabGridContainer, arrDOInDesigner)
    {
        //==== cols+rows
        objTabGridContainer.appendChild(this.renderPropertyGridSizeContainer(arrDOInDesigner)); 

        //==== padding
        objTabGridContainer.appendChild(this.renderPropertyPaddingContainer(arrDOInDesigner)); 
        
        //==== margin
        objTabGridContainer.appendChild(this.renderPropertyMarginContainer(arrDOInDesigner)); 

        //==== visibility
        objTabGridContainer.appendChild(this.renderPropertyVisibility(arrDOInDesigner)); 
          
        //==== move
        objTabGridContainer.appendChild(this.renderPropertyMove(arrDOInDesigner)); 
      
        //==== delete
        objTabGridContainer.appendChild(this.renderPropertyDelete(arrDOInDesigner)); 

    }    


    /**
     * renders padding-chapter for element-properties tab
     * 
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     * @returns {HTMLElement} Div
     */
    renderPropertyGridSizeContainer(arrDOInDesigner)
    {
        let objDivCol = null; //div column in grid
        let objLabel = null;

        //container
        const objDivColsRowsContainer = document.createElement("div");
        objDivColsRowsContainer.id = "gridsizecontainer";

        //label
        const objLblColsRows = document.createElement("label");
        objLblColsRows.classList.add("tab-element-property-header");
        objLblColsRows.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_elementproperties_grid_label_gridsize', 'Grid size') ?>";
        objDivColsRowsContainer.appendChild(objLblColsRows);

        //grid
        const objDivColsRowsGrid = document.createElement("div"); //all 4 padding properties
        objDivColsRowsGrid.style.display = "grid";
        objDivColsRowsGrid.style.gridTemplateColumns = "1fr 1fr";

            //==== columns
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_elementproperties_grid_label_columns', 'Columns') ?>";

            const objColumns = document.createElement("input");        
            objColumns.type = "number";
            objColumns.value = this.detectCols(arrDOInDesigner[0].firstChild);
            objColumns.min = 1;

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objColumns);
            objDivColsRowsGrid.appendChild(objDivCol);

            objColumns.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();  
                this.updateCols(arrDOInDesigner[0].firstChild, objColumns.value);
            },{signal: objElementTabAbortController.signal}); 
          
            objColumns.addEventListener("change", e =>  //"change" so the updown buttons make it change directly
            {
                addUndoState();                
                this.updateCols(arrDOInDesigner[0].firstChild, objColumns.value);
            },{signal: objElementTabAbortController.signal});     

            //==== rows
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_elementproperties_grid_label_rows', 'Rows') ?>";

            const objRows = document.createElement("input");        
            objRows.type = "number";
            objRows.value = this.detectRows(arrDOInDesigner[0].firstChild);
            objRows.min = 1;

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objRows);
            objDivColsRowsGrid.appendChild(objDivCol);

            objRows.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();   
                this.updateRows(arrDOInDesigner[0].firstChild, objRows.value);
            },{signal: objElementTabAbortController.signal}); 
          
            objRows.addEventListener("change", e =>  //"change" so the updown buttons make it change directly
            {
                addUndoState();
                this.updateRows(arrDOInDesigner[0].firstChild, objRows.value);
            },{signal: objElementTabAbortController.signal});        

        objDivColsRowsContainer.appendChild(objDivColsRowsGrid)//add rows+cols properties        


        //gap
        objDivCol = document.createElement("div");

        objLabel = document.createElement("label");
        objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_elementproperties_grid_label_gap', 'Gap (px)') ?>";

        const objGap = document.createElement("input");        
        objGap.type = "number";
        objGap.value = sanitizeStringNumber(arrDOInDesigner[0].firstChild.style.gap, "px");//take value of first element
        objGap.min = 0;

        objDivCol.appendChild(objLabel);
        objDivCol.appendChild(objGap);
        objDivColsRowsContainer.appendChild(objDivCol);

        objGap.addEventListener("keyup", e =>  //"keyup" so it responds directly
        {
            addUndoState();   
            for(let objDO of arrDOInDesigner)
            {
                objDO.firstChild.style.gap = objGap.value + "px";
                console.log("home hiereeeeeee");
            }
        },{signal: objElementTabAbortController.signal}); 
        
        objGap.addEventListener("change", e =>  //"change" so the updown buttons make it change directly
        {
            addUndoState();
            for(let objDO of arrDOInDesigner)
            {
                objDO.firstChild.style.gap = objGap.value + "px";
                console.log("home hiereeeeeee");
            }
        },{signal: objElementTabAbortController.signal});        


        return objDivColsRowsContainer;
    }  
    

    /**
     * set new number of rows (add or delete them)
     * @param {integer} iNewRowCount
     */
    updateRows(objParentContainer, iNewRowCount)
    {            
        let objDOContainer = new DOContainer();
        let iRowsDiff = iNewRowCount- this.detectRows(objParentContainer); //when positive: add rows, when negative: delete rows
        let iDetectedCols = this.detectCols(objParentContainer);

        iNewRowCount = parseInt(iNewRowCount); //from editbox it tends to be a string instead of int
        objDOContainer.renderDesigner();

        if (iNewRowCount > 0)
        {
            if (iRowsDiff >= 0)//creating cells
            {
                for (let iRowCounter = 0; iRowCounter < iRowsDiff; iRowCounter++) //for each row            
                    for (let iColCounter = 0; iColCounter < iDetectedCols; iColCounter++) //create x amount of columns
                        objParentContainer.appendChild(objDOContainer.cloneNode(true));                
            }
            else //delete cells
            {
                iRowsDiff = iRowsDiff * -1;//make positive 
                for (let iRowCounter = 0; iRowCounter < iRowsDiff; iRowCounter++) //for each row            
                    for (let iColCounter = 0; iColCounter < iDetectedCols; iColCounter++) //delete x amount of columns
                        objParentContainer.removeChild(objParentContainer.lastChild);
            }
        }
    }   

    /**
     * set a new number of cols (add or delete them)
     * 
     * @param {HTMLElement} objParentContainer --> container <div> of the parent grid
     * @param {integer} iNewColCount
     */
    updateCols(objParentContainer, iNewColCount)
    {      
        let objDOContainer = new DOContainer();
        let iDetectedCols = this.detectCols(objParentContainer);
        let iDetectedRows = this.detectRows(objParentContainer);
        let iColsDiff = iNewColCount - iDetectedCols; //when positive: add cols, when negative: delete cols
        let iIndexLastColumnOfRow = 0;
        let iColOffset = 0;

        iNewColCount = parseInt(iNewColCount); //from editbox it tends to be a string instead of int
        objDOContainer.renderDesigner();

        if (iNewColCount)
        {            
            if (iColsDiff >= 0)//creating cells
            {
                for (let iRowCounter = 0; iRowCounter < iDetectedRows; iRowCounter++) //for each row            
                {
                    //insert cell after last cell-in-the-column of each row
                    iColOffset = iRowCounter * iColsDiff; //every time we add a div, the index of the last column in a row changes
                    iIndexLastColumnOfRow = (iRowCounter*iDetectedCols) + (iDetectedCols-1) + iColOffset;

                    for (let iColCounter = 0; iColCounter < iColsDiff; iColCounter++) //create x amount of columns
                        objParentContainer.children[iIndexLastColumnOfRow].after(objDOContainer.cloneNode(true));                
                }

                //update grid info
                objParentContainer.style.gridTemplateColumns = this.getGridTemplateColumnsText(iNewColCount);
            }
            else //delete cells
            {
               /*
                explanation with example:
                let's assume: going from 5 (old) to 3 (new) columns
                iterate indexes: for every 3 indexes
                    remove 2 (=5-3=iColsDiff) on the same index (because index changes when you remove)
               */

                iColsDiff = iColsDiff * -1;//make positive 
                const iNumCellsToRemove = iDetectedRows * iColsDiff;
                const iMaxIndex = (iDetectedRows * iDetectedCols) - iNumCellsToRemove;                
                var iIndexCounter = 0;
                var iLoopCounter = 0;

                while(iIndexCounter < iMaxIndex)
                {
                    iIndexCounter+=iNewColCount; //steps of 3 (assuming we go from 5 to 3 columns)
                    iLoopCounter++;
                    iColOffset = iLoopCounter * iColsDiff; //every time we remove a div, the index changes

                    for (let iRemoveCount = 0; iRemoveCount < iColsDiff; iRemoveCount++) //iterate 2x to delete (assuming we go from 5 to 3 columns)
                        objParentContainer.removeChild(objParentContainer.children[iIndexCounter]); //remove from the same index (because the index changes when we remove children)
                }

                //update grid info
                objParentContainer.style.gridTemplateColumns = this.getGridTemplateColumnsText(iNewColCount);                

            }
        }
    }  

    /**
     * render text for HTMLElement.style.gridTemplateColumns
     * based on the amount of columns
     */
    getGridTemplateColumnsText(iColumnCount)
    {
        let sGridTemplateColumnsText = 0;

        for (let iCounter = 0; iCounter < iColumnCount; iCounter++) //i choose 1fr 1fr 1fr instead of repeat(3,1fr) because you could let the user specify 2fr,3fr etc in the editor. it's also easier to determine the amount of colums (just count the number of spaces + 1)
        {
            if (iCounter > 0) //add space before text, except the first one
                sGridTemplateColumnsText += " ";
            sGridTemplateColumnsText += "1fr";
        }
        
        return sGridTemplateColumnsText;
    }

    /**
     * determine # of rows in the grid by looking at its HTML element
     * 
     * @param {HTMLElement} objParentContainer --> container <div> of the parent grid
     */        
    detectRows(objParentContainer)
    {
        return Math.round(objParentContainer.childElementCount / this.detectCols(objParentContainer));  //we only take the first DesignObject if multiple are selected
    }

    /**
     * determine # of columns
     * 
     * @param {HTMLElement} objParentContainer --> container <div> of the parent grid
     */
    detectCols(objParentContainer)
    {
        return objParentContainer.style.gridTemplateColumns.split(" ").length; //we only take the first DesignObject if multiple are selected
    }    
}
