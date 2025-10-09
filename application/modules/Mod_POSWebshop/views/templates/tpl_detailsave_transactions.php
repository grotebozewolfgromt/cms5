<style>
    /* we do the stylesheet inline, because it only applies to this page */
    .transactionlines-grid-row
    {
        display: grid;
        /* grid-template-columns: repeat(auto-fit, minmax(100px,1fr)); */
        grid-template-columns: 1fr 4fr 1fr 1fr 1fr 1fr 1fr;

        grid-column-gap: 10px;
        /* background-color:#efefef; */
        margin-bottom: 10px;
        padding: 10px;
        /* background-color: #f1f7fde2; */
        /* background-color: light-dark(var(--lightmode-color-background-site), var(--darkmode-color-background-site)); */
        border-color: light-dark(var(--lightmode-color-border), var(--darkmode-color-border));
        border-style: solid;
        border-width: 1px;
    }

    .formsection-line input[type=text]
    {
        width: 100%;
    }

    #template-transactionslines
    {
        display: none;
    }


</style>

<script>
    /* we do the javascript inline, because it only applies to this page */
    
    /**
     * function addTransactionsLine()
     * 
     * handles all the actions from the button 'add line'
     */
    function addTransactionsLine()
    {
        objParentNode = document.getElementsByClassName('transactionlines-grid')[0];

        //retrieve the template line + duplicate it
        objTemplateDiv = document.getElementsByClassName('transactionlines-grid-row')[0]; //take the first line
        objNewLine = objTemplateDiv.cloneNode(true);
        objNewLine.id = '';//we want to get rid of the id to make it visible (double ids = not good)
        
        //empty the elements on the line (because it is a copy, it contains all the data from the first line as well)
        objNewLine.children[0].children[1].value = '1'; //QUANTITY: traverse: DIV[0], skip DIV[0], use INPUT[1]  
        objNewLine.children[1].children[1].value = ''; //DESCRIPTION: traverse: DIV[1], skip DIV[0], use INPUT[1]  
        objNewLine.children[2].children[1].value = ''; //VAT: traverse: DIV[2], skip DIV[0], use INPUT[1]  
        objNewLine.children[3].children[1].value = ''; //PURCHASE: traverse: DIV[3], skip DIV[0], use INPUT[1]  
        objNewLine.children[4].children[1].value = ''; //DISCOUNT: traverse: DIV[4], skip DIV[0], use INPUT[1]  
        objNewLine.children[5].children[1].value = ''; //UNIT PRICE: traverse: DIV[6], skip DIV[0], use INPUT[1]  


        //temp remove the button (otherwise the new line is added below button)
        objButtonAddLine = document.getElementById('transactions-button-add-line');
        objParentNode.removeChild(objButtonAddLine);

        //add new line node       
        objParentNode.appendChild(objNewLine);
        
        //add button again
        objParentNode.appendChild(objButtonAddLine);
    }   

    /**
     * function deleteTransactionsLine()
     * 
     * deletes a line in a transaction
     */
    function deleteTransactionsLine(objDeleteButton)
    {
        //find the line to remove
        objRemoveLine = objDeleteButton.parentElement.parentElement;
        objRemoveLineParent = objRemoveLine.parentElement;

        //prevent last line from being removed
        if(objRemoveLineParent.getElementsByClassName('formsection-line').length > 1)
        {
            //remove line from parent div met alle lines
            objRemoveLineParent.removeChild(objRemoveLine);
        }
        else
        {      
            // alert('ouch!');
            alert("<?php echo transm(CMS_CURRENTMODULE, 'button_removeline_error_cantremovelastline', 'Ouch! You need to have at least 1 line!'); ?>");
        }

    }    

</script>

<form id="detailsave" name="detailsave" method="post" action="<?php echo getURLThisScript(); ?>">

    <!-- form obligatory fields -->
    <?php echo $objController->objHidFormSubmitted->renderHTMLNode(); //able to detect if form is submitted via FormGenerator ?>
    <?php echo $objController->objHidCSRFToken->renderHTMLNode(); //able to detect Cross Site Request Forgery via FormGenerator ?>


    <!-- the details section -->
    <div class="formsection">
        <div class="formsection-header"><?php echo transm($objController->getModule(), 'detailsave_transactions_section_details_name', 'Details'); ?></div>

        <!-- invoice type -->
        <div class="formsection-line">
            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_transactiontype_description', 'Transaction type'); ?></div>
            <?php echo $objController->objSelTransactionsType->renderHTMLNode(); ?>
        </div>

        <!-- currency -->
        <div class="formsection-line">
            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_currency_description', 'Currency'); ?></div>
            <?php echo $objController->objSelCurrency->renderHTMLNode(); ?>
        </div>       

        <!-- buyer -->
        <div class="formsection-line">
            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_buyer_description', 'Buyer'); ?></div>
            <?php echo $objController->objHidBuyer->renderHTMLNode(); ?>
        </div>         
        
        <!-- purchase order number -->
        <div class="formsection-line">
            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_purchaseorderno_description', 'Purchase order number'); ?></div>
            <?php echo $objController->objEdtPurchaseOrderNo->renderHTMLNode(); ?>
        </div>          

    </div> 


    <!-- the lines section -->
    <div class="formsection">
        <div class="formsection-header"><?php echo transm($objController->getModule(), 'detailsave_transactions_section_lines_name', 'Lines'); ?></div>

        <div class="transactionlines-grid">


            <?php 
                $objController->objTransactionLines->resetRecordPointer();
                while($objController->objTransactionLines->next())
                {
                    $objController->objEdtQuantity->setValue($objController->objTransactionLines->getQuantity()->getValue());
                    $objController->objEdtDescription->setValue($objController->objTransactionLines->getDescription());
                    $objController->objEdtVATPercentage->setValue($objController->objTransactionLines->getVATPercentage()->getValue());
                    $objController->objEdtPurchasePriceExclVAT->setValue($objController->objTransactionLines->getUnitPurchasePriceExclVAT()->getValue());
                    $objController->objEdtDiscountPriceExclVAT->setValue($objController->objTransactionLines->getUnitDiscountExclVat()->getValue());
                    $objController->objEdtPriceExclVAT->setValue($objController->objTransactionLines->getUnitPurchasePriceExclVAT()->getValue());
                    ?>

                    <div class="formsection-line transactionlines-grid-row">
                        <!-- quantity -->
                        <div>
                            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_quantity_description', 'Quantity'); ?></div>
                            <?php echo $objController->objEdtQuantity->renderHTMLNode(); ?>
                        </div>

                        <!-- description -->
                        <div>
                            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_description_description', 'Description'); ?></div>                    
                            <?php echo $objController->objEdtDescription->renderHTMLNode(); ?>
                        </div>

                        <!-- vat percentage -->
                        <div>
                            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_vatpercentage_description', 'VAT %'); ?></div>                                        
                            <?php echo $objController->objEdtVATPercentage->renderHTMLNode(); ?>
                        </div>                

                        <!-- purchase price -->
                        <div>
                            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_purchasepriceexclvat_description', 'Purchase price *'); ?></div>                                        
                            <?php echo $objController->objEdtPurchasePriceExclVAT->renderHTMLNode(); ?>
                        </div>   
                        
                        <!-- discount price -->
                        <div>
                            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_discountprice_description', 'Discount *'); ?></div>                                        
                            <?php echo $objController->objEdtDiscountPriceExclVAT->renderHTMLNode(); ?>
                        </div>               
                        
                        <!-- unit price excluding vat -->
                        <div>
                            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_unitpriceexclvat_description', 'Unit price *'); ?></div>                                        
                            <?php echo $objController->objEdtPriceExclVAT->renderHTMLNode(); ?>
                        </div>                   

                        <!-- delete -->
                        <div>
                            <input type="button" onclick="deleteTransactionsLine(this)" value="X" class="button_normal" style="background-color: red;">                                        
                        </div>   
                        
                    </div>

                    <?php
                }
            ?>
            
            * excluding VAT<br>

            <!-- 'add line'-button -->
            <input type="button" onclick="addTransactionsLine()" value="<?php echo transm($objController->getModule(), 'detailsave_transactions_button_addline', 'Add line +'); ?>" class="button_normal" id="transactions-button-add-line">
        </div><!-- END transactionlines-grid -->
        
    </div><!-- END formsection --> 

    
    <!-- notes section -->
    <div class="formsection">
        <div class="formsection-header"><?php echo transm($objController->getModule(), 'detailsave_transactions_section_notes_name', 'Notes'); ?></div>


        <!-- internal notes -->
        <div class="formsection-line">
            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_internalnotes_description', 'Internal notes - only visible to you'); ?></div>
            <?php echo $objController->objTxtNotesInternal->renderHTMLNode(); ?>
        </div>       

        <!-- external notes -->
        <div class="formsection-line">
            <div class="form-description" for=""><?php echo transm($objController->getModule(), 'detailsave_transactions_field_externalnotes_description', 'External notes - shown on invoice'); ?></div>
            <?php echo $objController->objTxtNotesExternal->renderHTMLNode(); ?>
        </div>        
    </div> 



    <!-- history section -->
    <div class="formsection">
        <div class="formsection-header"><?php echo transm($objController->getModule(), 'detailsave_transactions_section_history_name', 'History'); ?></div>

    </div> 


    <!-- command panel -->
    <div class="formsection div_commandpanel">
        <?php echo $objController->objSubmit->renderHTMLNode(); ?>
        <?php echo $objController->objCancel->renderHTMLNode(); ?>
    </div> 

</form>
