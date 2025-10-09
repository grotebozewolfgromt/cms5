<?php
/**
 * In this library we can do speed tests
 * this enables me to store test scenarios and revisit them later, and do minor tweaks
 * 
 * 15 mei 2024: created
 * @author Dennis Renirie
 */


 /**
  * test for loop against foreach for array traversal
  */
function foreachVSfor()
{
    //fill test array
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
        $arrTest[] = $iTeller;



    starttest('forloop1');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        for ($iTest = 0; $iTest < 1000; $iTest++)
        {
            echo $arrTest[$iTest];
        }
    }
    stoptest('forloop1');
    
    
    starttest('foreachloop1');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        foreach ($arrTest as $sTemp)
        {
            echo $sTemp;
        }
    }
    stoptest('foreachloop1');
    
    
    starttest('forloop2');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        for ($iTest = 0; $iTest < 1000; $iTest++)
        {
            echo $arrTest[$iTest];
        }
    }
    stoptest('forloop2');
    
    
    starttest('foreachloop2');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        foreach ($arrTest as $sTemp)
        {
            echo $sTemp;
        }
    }
    stoptest('foreachloop2');        
}

 


function explodeVSstrpos()
{
    $sTestString = 'aslkd asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf asdf';

    $arrExplode = array();
    $sPart1 = '';
    $sPart2 = '';
    $iPos = 0;
    $iLen = 0;

    starttest('split1a');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $arrExplode = explode(' ', $sTestString);
        $sPart1 = $arrExplode[0];
        $sPart2 = $arrExplode[1];
    }
    stoptest('split1a');


    starttest('split1b');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $arrExplode = explode(' ', $sTestString);
        $sPart1 = $arrExplode[0];
        $sPart2 = $arrExplode[1];
    }
    stoptest('split1b');

    starttest('split2a');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $iPos = strpos($sTestString, ' ');
        $iLen = strlen($sTestString);
        $sPart1 = substr($sTestString, 0,  $iPos);
        $sPart2 = substr($sTestString, $iPos,  $iLen-$iPos);
    }
    stoptest('split2a');

    starttest('split2b');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $iPos = strpos($sTestString, ' ');
        $iLen = strlen($sTestString);
        $sPart1 = substr($sTestString, 0,  $iPos);
        $sPart2 = substr($sTestString, $iPos,  $iLen-$iPos);
    }
    stoptest('split2b');
}

function explodeVSstrposVar2()
{
    $sTestString = 'aslkdsdfgsdfgs dfasdf asdfg';

    $arrExplode = array();
    $sPart1 = '';
    $sPart2 = '';
    $iPos = 0;
    $iLen = 0;
    $iCount = 0;

    starttest('split1a-explode');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $arrExplode = explode(' ', $sTestString);
        $iCount = count($arrExplode);        
        if ($iCount > 0)
            $sPart1 = $arrExplode[0];
        //$sPart2 = $arrExplode[1];
    }
    stoptest('split1a-explode');


    starttest('split1b-explode');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $arrExplode = explode(' ', $sTestString);
        $iCount = count($arrExplode);
        if ($iCount > 0)
            $sPart1 = $arrExplode[0];
        // $sPart2 = $arrExplode[1];
    }
    stoptest('split1b-explode');

    starttest('split2a-strpos');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $iPos = strpos($sTestString, ' ');
        // $iLen = strlen($sTestString);
        if ($iPos !== false)
            $sPart1 = substr($sTestString, 0,  $iPos);
        // $sPart2 = substr($sTestString, $iPos,  $iLen-$iPos);
    }
    stoptest('split2a-strpos');

    starttest('split2b-strpos');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $iPos = strpos($sTestString, ' ');
        // $iLen = strlen($sTestString);
        if ($iPos !== false)
            $sPart1 = substr($sTestString, 0,  $iPos);
        //$sPart2 = substr($sTestString, $iPos,  $iLen-$iPos);
    }
    stoptest('split2b-strpos');
}




function strreplaceVSexplode()
{
    $sTestString = 'Lorem Ipsum';
    $sResult = '';
    starttest('strreplace1');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $sResult.=str_replace(' ', '-', $sTestString);
    }
    stoptest('strreplace1');
    
    starttest('strreplace2');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $sResult.=str_replace(' ', '-', $sTestString);
    }
    stoptest('strreplace2');
    
    
    $arrExpl = array();
    starttest('explodereplace1');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $arrExpl = explode(' ', $sTestString);
        $sResult.= implode('-', $arrExpl);
    }
    stoptest('explodereplace1');
    
    
    $arrExpl = array();
    starttest('explodereplace2');
    for ($iTeller = 0; $iTeller < 1000; $iTeller++)
    {
        $arrExpl = explode(' ', $sTestString);
        $sResult.= implode('-', $arrExpl);
    }
    stoptest('explodereplace2');
}


function strreplaceVSsubstring()
{
    $sTestString = 'blaat[]';
    $sNewString = '';
    $iPos = 0;


    starttest('strreplace1');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        $sNewString = str_replace('[', '', $sTestString);
    }
    stoptest('strreplace1');
        
    starttest('strreplace2');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        $sNewString = str_replace('[', '', $sTestString);
    }
    stoptest('strreplace2');
        

    starttest('substr1');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        $iPos = strpos($sTestString, '[');
        // $iLen = strlen($sTestString);
        $sNewString = substr($sTestString, $iPos, 1);     
    }
    stoptest('substr1');

    starttest('substr2');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        $iPos = strpos($sTestString, '[');
        // $iLen = strlen($sTestString);
        $sNewString = substr($sTestString, $iPos, 1);        
    }
    stoptest('substr2');

}

//ik wil de laatste 2 characters strippen
function strreplaceVSsubstringVar2()
{
    $sTestString = 'blaat[]';
    $sNewString = '';
    $iPos = 0;


    starttest('strreplace1');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        $sNewString.= str_replace('[', '', $sTestString);
        $sNewString.= str_replace(']', '', $sTestString);
    }
    stoptest('strreplace1');
        
    starttest('strreplace2');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        $sNewString.= str_replace('[', '', $sTestString);
        $sNewString.= str_replace(']', '', $sTestString);
    }
    stoptest('strreplace2');
        

    starttest('substr1');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        $iPos = strpos($sTestString, '[');
        $sNewString.= substr($sTestString, $iPos, 2);     
    }
    stoptest('substr1');

    starttest('substr2');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        $iPos = strpos($sTestString, '[');
        $sNewString.= substr($sTestString, $iPos, 2);     
    }
    stoptest('substr2');


    echo $sNewString;
}

function inarrayVSstrindex()
{
    $iTemp =0;
    $arrVOIDTAGS = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'); //Singleton Tags with no closing tags
    $sTagName = 'blaat';
    $sInput = ' asdfasdfkj asdlf jhasd lfk jhas dflkjashd laksjhdf laskjdfh laskjhdf laksjh flaskjhdf alskjdhf alskjd fhalskjd fha<href="">';
    $iPosGTBeginTag = 10;

    starttest('inarray1');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        if (!in_array($sTagName, $arrVOIDTAGS))
            $iTemp++;
    }
    stoptest('inarray1');    

    starttest('inarray2');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        if (!in_array($sTagName, $arrVOIDTAGS))
            $iTemp++;
    }
    stoptest('inarray2');    
    

    starttest('arrindex1');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        if ($sInput[$iPosGTBeginTag - 1] != '/')
            $iTemp++;
    }
    stoptest('arrindex1');        


    starttest('arrindex2');
    for ($iTeller = 0; $iTeller < 10000; $iTeller++)
    {
        if ($sInput[$iPosGTBeginTag - 1] != '/')
            $iTemp++;
    }
    stoptest('arrindex2');        


    echo $iTemp;

}


?>
