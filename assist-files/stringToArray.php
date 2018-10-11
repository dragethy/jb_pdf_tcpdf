<?php

/* START
 * // array of arrays
 * $matches[0][0] = 'Strings matched';
 * $matches[1][0] = 'method from [0][0]';
 * $matches[2][0] = 'params from [0][0]';
 * $matches[3][0] = 'types from [0][0]';
 *
 *
 * // convert to arrays
 * $matches[2][0]
 * $matches[3][0]
 *
 * /-- 1 --/
 * @return value to $param[];
 *
 * // if has array
 * i.e. "array(stuff),stuff"
 *
 * // else if has comma
 * i.e. "stuff,array(stuff)"
 *
 * // else is last item
 * i.e. "stuff"
 *
 * /-- 2 --/
 * @return array to $param[n]
 *
 * // use same process as /-- 1 --/
 *
 * /-- 3 --/
 * @return assoc array to $param[n]
 *
 * // if has =>
 * i.e. "stuff,array(stuff => value)"
 *
 * /-- 4 --/
 * @return values and keys with string characters and whitespace removed
 * i.e. ' and "
 *
 **/
 
 
// $string = $matches[2][n];
function _tcpdfArrayFromStringWrapper($string)
{
  
    // need a value to loop against, length of string seems good place to start
    $count = strlen($string);
    
    // place to store data
    $manipulated = array();
    
    // establish array by looping through $string, reassigning $string, break when there is no string left
    for($i = 0; $i < $count; $i++)
    {
    
        // $split is array of manipulated string and string to reiterate on
        $split = _tcpdfArrayFromString($string);
      
        // manipulated string
        $manipulated[] = $split[0];
        
        // string to manipulate further
        $string = $split[1];
        
        // reset $split
        $split = array();
    }
  
}



function _tcpdfArrayFromString($string)
{
    // if
    // check if starts with array and clean the keys and values
    // else
    // check if contains a comma and clean the keys and values
    // else
    // check if is meant to be assoc and clean the keys and values
    // else
    // nothing to do except clean the string and discontinue loop
    
    // check with cascading in order of requirement
    if(strpos( $string, 'array(' ) !== false)
    {
        
        // assign splitting criteria
        $condition = 'array(';
        $splitter = ')';
        // $trim is pretty consistent, should be function
        $trim[0][0] = 'array('; // the remainder trims are same
        $trim[1][0] = "'";
        $trim[1][1] = " ";
        $trim[1][2] = " \t\n";
        
    }
    elseif(strpos( $string, ',' ) !== false)
    {
        
        // assign splitting criteria
        $condition = ',';
        $splitter = ',';
        $trim[0][0] = '';
        $trim[1][0] = "'";
        $trim[1][1] = " ";
        $trim[1][2] = " \t\n";
        
    }
    elseif(strpos( $string, '=>' ) !== false)
    {
        
        // assign splitting criteria
        $condition = '=>';
        $splitter = '=>';
        $trim[0][0] = '';
        $trim[1][0] = "'";
        $trim[1][1] = " ";
        $trim[1][2] = " \t\n";
        
    }
    else
    {
        
        // assign splitting criteria
        $condition = '';
        $splitter = '';
        $trim[0][0] = '';
        $trim[1][0] = "'";
        $trim[1][1] = " ";
        $trim[1][2] = " \t\n";
        
    }
    
    
    // Now we have the match from above, we can act on it
    // This function will call itself as a way to check each value received
    // i.e. if it encounters an array, ', or =>'
    // split string at ')', $remove and $trim
    if($splitter === '')
    {
        
        $split[0] = $string;
        $split[1] = '';
        
    }
    else
    {

        $split = explode($splitter, $string);

        // clean $plit[1]
        foreach($trim[0] as $key => $value)
        {
            
            $split[1] = trim($split[1]);
    
        }
        
    }

    
    
    
    switch($condition)
    {
        case 'array(':
            // split string at ')', $remove and $trim
            $split = explode(')', $string);
            $split[0] = trim();
            break;
            
        case ',':
            // split string at ',', $remove and $trim
            
            break;

        case '=>':
            // split string at '=>', $remove and $trim
            
            break;

        default:
            
            break;
    }
  
    

}
