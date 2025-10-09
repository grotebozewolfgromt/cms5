<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <script>
            var es;
            
            function startTask() {
                es = new EventSource('sse_progress_backend.php');
                
                //a message is received
                es.addEventListener('message', function(e) {
                    console.log(e.data);
                    console.log(e.id);
                    var result = JSON.parse( e.data );
                    
                    addLog(result.message);       
                    
                    if(e.lastEventId == 'CLOSE') {
                        addLog('Received CLOSE closing');
                        es.close();
                        var pBar = document.getElementById('progressor');
                        pBar.value = pBar.max; //max out the progress bar
                    }
                    else {
                        var pBar = document.getElementById('progressor');
                        pBar.value = result.progress;
                        var perc = document.getElementById('percentage');
                        perc.innerHTML   = result.progress  + "%";
                        perc.style.width = (Math.floor(pBar.clientWidth * (result.progress/100)) + 15) + 'px';
                    }
                });
                
                es.addEventListener('error', function(e) 
                {
                    addLog('Error occurred');
                    es.close();
                });
            }
            
            function stopTask() {
                es.close();
                addLog('Interrupted');
            }
            
            function addLog(message) {
                var r = document.getElementById('results');
                r.innerHTML += message + '<br>';
                r.scrollTop = r.scrollHeight;
            }            
        </script>
    </head>
    <body>
        <br />
        <input type="button" onclick="startTask();"  value="Start Long Task" />
        <input type="button" onclick="stopTask();"  value="Stop Task" />
        <br />
        <br />
         
        <p>Results</p>
        <br />
        <div id="results" style="border:1px solid #000; padding:10px; width:300px; height:250px; overflow:auto; background:#eee;"></div>
        <br />
         
        <progress id='progressor' value="0" max='100' style=""></progress>  
        <span id="percentage" style="text-align:right; display:block; margin-top:5px;">0</span>
    </body>
</html>