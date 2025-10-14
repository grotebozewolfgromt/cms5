<?php
/**
 * New method installation file based on TInstallScreen
 * 
 * installler/step1.php created 17-8-2025
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=300, initial-scale=1"> 
        <link rel="icon" type="image/x-icon" href="favicon.svg">
        <script>
                var objEventSource = null;
                
                function startTask() 
                {
                        objEventSource = new EventSource('<?php echo $sURLSSE; ?>');
                        
                        //a message is received
                        objEventSource.addEventListener('message', function(objEvent) 
                        {
                                var objJSONData = JSON.parse( objEvent.data );
                                // console.log('yolkp', objEvent.data);

                                addLog(objJSONData.message);       

                                if(objEvent.lastEventId == 'CLOSE')//received: close connection
                                {
                                        // addLog('<br>Closing connection<br>');
                                        objEventSource.close();

                                        //global progress
                                        const objPbGlobal = document.getElementById('pbGlobal');
                                        objPbGlobal.value = objPbGlobal.max; //max out the progress bar
                                        const objSpGlobalPercentage = document.getElementById('spGlobalPercentage');
                                        objSpGlobalPercentage.innerHTML   = "100%";

                                        //sub progress
                                        const objPbSub = document.getElementById('pbSub');
                                        objPbSub.value = objPbSub.max;   //max out the progress bar                                      
                                        const objSpSubPercentage = document.getElementById('spSubPercentage');
                                        objSpSubPercentage.innerHTML   =  "100%";    
                                        
                                        //enable next button
                                        const objBtnNext = document.getElementById('btnNext');
                                        objBtnNext.disabled = false;
                                }
                                else //normal processing
                                {
                                        //global progress
                                        let objPbGlobal = document.getElementById('pbGlobal');
                                        objPbGlobal.value = objJSONData.progressglobal;
                                        objPbGlobal.max = objJSONData.progressglobalmax;
                                        let objSpGlobalPercentage = document.getElementById('spGlobalPercentage');
                                        objSpGlobalPercentage.innerHTML   = Math.round((100/objJSONData.progressglobalmax) * objJSONData.progressglobal) + "%";

                                        //sub progress
                                        let objPbSub = document.getElementById('pbSub');
                                        objPbSub.value = objJSONData.progresssub;    
                                        objPbSub.max = objJSONData.progresssubmax;
                                        let objSpSubPercentage = document.getElementById('spSubPercentage');
                                        objSpSubPercentage.innerHTML   = Math.round((100/objJSONData.progresssubmax) * objJSONData.progresssub) + "%";
                                }
                        });
                        
                        objEventSource.addEventListener('error', function(objEvent) 
                        {
                                addLog('<br><b><span class="error">Error occurred</span></b>');
                                objEventSource.close();
                        });
                }
            
                function stopTask()
                {
                        objEventSource.close();
                        addLog('<br>Interrupted<br>');
                }

                function addLog(message) 
                {
                        let objDiv = document.getElementById('dvLogPanel');
                        objDiv.innerHTML += message;
                        objDiv.scrollTop = objDiv.scrollHeight;
                }            



            /**
             * when clicked on the "next" button
             */
            function handleNextButton()
            {
                const objForm = document.getElementById("frmBody");
                const objBtnNext = document.getElementById("btnNext");

                //prevent user from clicking twice
                objBtnNext.disabled = true;
                objBtnNext.textContent = "Wait";

                if(objForm)
                {
                        objForm.submit();
                }
                else
                        window.location.href = "<?php echo $sURLNextButton; ?>";
            }

            /**
             * when clicked on the "previous" button
             */
            function handlePreviousButton()
            {
                const objBtnPrevious = document.getElementById("btnPrevious");

                //prevent user from clicking twice
                objBtnPrevious.disabled = true; 
                objBtnPrevious.textContent = "Wait";               

                window.location.href = "<?php echo $sURLPreviousButton; ?>";
            }
        </script>
        <style>
                /* prevent overflow editboxes */
                * 
                {
                        padding: 0; 
                        margin: 0;
                        box-sizing: border-box;
                }

                body
                {
                        font-size: 0.85rem;
                        line-height: 1.3rem;
                        color: #000000;
                        font-family: verdana;
                        box-sizing: border-box;
                        /* max-width: 100%; */
                }

                /* contentpanel includes headerpanel, bodypanel and command panel */
                .contentpanel
                {
                        background-color: #f4f4f4ff;
                        border-color: #cdcdcdff;
                        border-width: 1px;
                        border-style: solid;
                        /* min-width: 200px;
                        max-width: 800px; */
                        margin-left: auto;
                        margin-right: auto;
                        min-height: 200px;
                        box-sizing: border-box;
                        border-radius: 5px;
                        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                }

                .headerpanel
                {
                        background-color: #ffffffff;
                        border-color: #cdcdcdff;
                        border-width: 0px;
                        border-bottom-width: 1px;
                        border-style: solid;
                        padding: 30px;
                        padding-bottom: 15px;
                        box-sizing: border-box;
                        border-top-left-radius: 5px;
                        border-top-right-radius: 5px;

                }
                
                .bodypanel
                {
                        background-color: #f4f4f4ff;
                        padding: 30px;
                        box-sizing: border-box;
                        min-height: 300px;
                }  

                .commandpanel
                {
                        background-color: #f4f4f4ff;
                        border-color: #cdcdcdff;
                        border-width: 0px;
                        border-top-width: 1px; 
                        border-style: solid;                       
                        border-bottom-left-radius: 5px;
                        border-bottom-right-radius: 5px;                        
                        padding: 30px;
                        box-sizing: border-box;
                        height: 100px;
                }                

                h1
                {
                        font-size: 1.2rem;
                        margin-top: 0rem;
                        margin-bottom: 0.7rem;
                }

                h2
                {
                        font-size: 0.9rem;
                        margin-bottom: 0.5rem;
                        margin-top: 0.5rem;
                }

                form
                {
                        box-sizing: border-box;
                }

                iframe
                {
                        border-color: #cdcdcdff;
                        border-width: 1px;
                        border-style: solid;
                        border-radius: 5px;
                }
                
                li
                {
                        margin-left: 15px;
                }

                #dvLogPanel
                {
                        background-color: white;
                        border-color: #cdcdcdff;
                        border-width: 1px;
                        border-style: solid;
                        border-radius: 5px;
                        overflow-y: scroll;
                        height: 200px;
                        padding: 10px;
                        margin-top: 30px;
                }

                .ahrefbutton, button, input[type=button], input[type=submit]
                {
                        background-color: #046aaa;
                        border: none;
                        color: white;
                        padding: 12px 12px;
                        text-decoration: none;
                        /* margin: 4px 2px; */
                        cursor: pointer;
                        border-radius: 10px;
                        font-weight: bolder;
                }

                button:disabled,
                button[disabled]{
                        border: 1px solid #999999;
                        background-color: #cccccc;
                        color: #666666;
                        cursor: not-allowed;
                        opacity: 0.5;
                }                

                input[type="text"],
                input[type="password"]
                {
                       width: 100%;
                       margin-top:5px;
                       margin-bottom:15px;
                }

                #btnNext
                {
                        float: right;
                        min-width: 100px;
                }

                #btnPrevious
                {
                        float: left;
                        min-width: 100px;
                }

                span.error
                {
                        color: red;
                        font-weight: bolder;
                }

                span.noerror
                {
                        color: green;
                        font-weight: bolder;
                }     
                
                span.warning
                {
                        color: #ff8001ff;
                        font-weight: bolder;
                }
                


                /* ======================================= SMALL SIZE SCREENS =========================== */
                @media all and (max-width: 768px)
                {
                        .contentpanel
                        {
                                /* min-width: 200px; */
                                max-width: 100%;
                        }                          
                }

                /* ======================================= MEDIUM SIZE SCREENS ========================= */
                @media all and (min-width: 768px) AND (max-width: 1024px)
                { 
                        .contentpanel
                        {
                                /* min-width: 200px; */
                                max-width: 100%;
                        }                          
                }

                /* ======================================= LARGE SIZE SCREENS ========================== */
                @media all and (min-width: 1024px)
                {
                        .contentpanel
                        {
                                margin-top: 50px;
                                min-width: 200px;
                                max-width: 800px;
                        }  
                }                
        </style>
        <title><?php echo $this->getTitle() ?></title>
    </head>
    <body>
        <div class="contentpanel">
                <div class="headerpanel">
                        <h1><?php echo $sTitle; ?></h1>
                        <?php echo $sDescription; ?>
                </div>
                <div class="bodypanel">
                        <form action="<?php echo $sURLNextButton; ?>" method="post" autocomplete="off" id="frmBody">
                                <?php echo $sBody; ?>
                        </form>
                </div>
                <div class="commandpanel">
                        <button id="btnNext" onClick="handleNextButton();"
                                <?php
                                        if (!$bNextButtonEnabled)
                                                echo ' disabled';
                                        echo '>';
                                        echo $this->getTextNextButton();
                                ?>
                        </button>
                        <button id="btnPrevious" onClick="handlePreviousButton();"
                                <?php
                                        if (!$bPreviousButtonEnabled)
                                                echo ' disabled';
                                        echo '>';
                                        echo $this->getTextPreviousButton();
                                ?>
                        </button>
                </div>

        </div>
    </body>
</html>