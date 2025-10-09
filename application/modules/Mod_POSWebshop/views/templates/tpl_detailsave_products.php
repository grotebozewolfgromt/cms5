<script>
    <?php
        include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'ajaxform.js'; //---> uses PHP
    ?>

    /* define inline because this is the only screen it applies to */
</script>

<style>
    /* define inline because this is the only screen it applies to */
    #product-basegrid
    {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }

    #product-grid-bottom-tabsheets
    {
        grid-column: 1 / span 2;
        /* background-color: blue; */
    }

</style>


<div id="detailsave-header">
    <div class="headercolumn headercolumn-exit">
        <button id="btnExit" onmousedown="handleExitAFC(exitAfterSave, document.querySelector('#btnExit dr-icon-spinner'))">
            <dr-icon-spinner>
                <svg class="iconchangecolor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" transform="rotate(180)">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>              
            </dr-icon-spinner>
            <div class="buttontext"><?php echo transm(CMS_CURRENTMODULE, 'detailsave_button_exit', 'Exit'); ?></div>        
        </button>
    </div>        
    <div class="headercolumn-title">
        <!-- no title (yet) -->
    </div>
    <div class="headercolumn headercolumn-save">
        <button id="btnSave" onmousedown="handleSaveAFC(null, document.querySelector('#btnSave dr-icon-spinner'))">
            <dr-icon-spinner>
                <svg id="svgSaveIcon" class="iconchangecolor" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>           
            </dr-icon-spinner>
            <div class="buttontext"><?php echo transm(CMS_CURRENTMODULE, 'detailsavetemplate_button_save', 'Save'); ?></div>        
        </button>                       
    </div>
</div>

<div id="detailsave-body">
    <div id="product-basegrid">
        <div id="product-grid-top-images">
            images 
        </div>
        <div id="product-grid-top-details">

            <!-- full product name -->
                <div class="formsection-line">
                    <label for="<?php echo $objEdtName->getId(); ?>">
                        <?php echo transm(CMS_CURRENTMODULE, 'productdetail_label_productnamefull', 'Name');?>
                        <dr-icon-info><?php echo transm(CMS_CURRENTMODULE, 'productdetail_label_productnamefull_info', 'Full name of the product');?></dr-icon-info>
                    </label>
                
                    <div class="formsection-line-errorlist"></div>
                    <?php echo $objEdtName->render(); ?>
                </div>

            <!-- short name -->
                <div class="formsection-line">
                    <label for="<?php echo $objEdtNameShort->getId(); ?>">
                        <?php echo transm(CMS_CURRENTMODULE, 'productdetail_label_productnameshort', 'Short name (max 20 characters)');?>
                        <dr-icon-info><?php echo transm(CMS_CURRENTMODULE, 'productdetail_label_productnameshort_info', 'Shortened name is shown in places where there is little space,<br>like the small paper of a ticket printer');?></dr-icon-info>
                    </label>
                    <div class="formsection-line-errorlist"></div>
                    <?php echo $objEdtNameShort->render(); ?>
                </div>

            Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsum quis sequi ipsam voluptatem perferendis dolorum sunt officia quos ex architecto in, atque vero, velit quibusdam nihil, amet inventore nam voluptate!
            Dicta ducimus quisquam quibusdam deserunt officia reprehenderit magnam odit nobis soluta aliquam, adipisci earum quia minima vero sint ea, quam atque similique nulla. Quasi porro veniam animi, nam asperiores id.
            Ut quos expedita quibusdam aliquid officia consequatur iste quae dicta quasi pariatur vel neque maxime placeat, ducimus similique praesentium illo nisi ipsum ullam dolorem unde error! Nihil quae consequuntur a!
            Repellat et obcaecati sunt sapiente nesciunt, ipsam placeat totam alias commodi minima quod quasi deserunt facere dolorum dolor autem maxime asperiores harum voluptatem nostrum repudiandae corrupti earum iure provident. Quidem.
            Rerum voluptates alias possimus, magnam nostrum sint voluptas nihil eaque quod accusamus voluptatum quaerat a error cupiditate fugit autem officia vitae saepe cumque cum rem minus nam sed ipsam. Provident.
            Consequatur molestiae officia cum soluta doloremque sit amet aut vitae? Odit, quo. Architecto ipsam eius adipisci consequuntur incidunt facilis, a, earum assumenda quasi deserunt debitis nam dolorem laboriosam explicabo voluptatem?
            Omnis reprehenderit officia cupiditate eius nulla fugiat labore aut, vitae doloribus eveniet quam quibusdam beatae delectus ab quisquam aliquid alias earum harum non officiis. Alias repudiandae incidunt eveniet minus deleniti!
            Dolore odio cupiditate iste temporibus minus porro voluptate rem architecto mollitia similique eos optio, non quasi totam voluptatum doloribus aperiam recusandae nobis maiores culpa velit. Facilis ipsam nihil asperiores in?
            Temporibus dolorum quo consectetur fuga voluptatem beatae reprehenderit? Recusandae, dolore quod delectus, tempore nihil corporis minima quia odio, iure deleniti odit suscipit fugiat quisquam facere? Est tempora voluptatibus enim deserunt?
            Error, tenetur dignissimos facere nihil obcaecati adipisci nemo eligendi dicta magni saepe, totam neque id minima excepturi quaerat enim ullam iusto ex, labore cum. Molestiae vel ad voluptates laudantium facilis.
        </div>
        <div id="product-grid-bottom-tabsheets">
            <dr-tabsheets>
                <div label="<?php echo transm(CMS_CURRENTMODULE, 'productdetail_tab_details_label', 'Details');?>" description="<?php echo transm(CMS_CURRENTMODULE, 'productdetail_tab_details_description', 'Edit more details of the product');?>"> </div>
                <div label="<?php echo transm(CMS_CURRENTMODULE, 'productdetail_tab_sku_label', 'SKUs');?>" description="<?php echo transm(CMS_CURRENTMODULE, 'productdetail_tab_sku_description', 'Stock Keeping Units are variations of a product which you want to track stock of');?>"> </div>
            </dr-tabsheets>
        </div>
    </div>
</div>
