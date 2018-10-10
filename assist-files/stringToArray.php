<?php

// START

// array of arrays
$matches[0][0] = 'Strings matched';
$matches[1][0] = 'method from [0][0]';
$matches[2][0] = 'params from [0][0]';
$matches[3][0] = 'types from [0][0]';

// convert $matches[2][0] and $matches [3][0] in to arrays

// function to return first value if is array
// i.e. "array(stuff),stuff"

// function to return first value if is not array
// i.e. "stuff,array(stuff)"

// function to return assoc values
// i.e. "'dog' => 'cat'" becomes array(0 => $key, 1 => $value);

// function to return values and keys with string characters and whitespace removed
// i.e. ' and "

// $string = $match[1][n];
function stringSplitToArrayWrapper($string)
{
  
    // need a value to loop against, length of string seems good place to start
    $count = strlen($string);
    
    // loop through $string, reassigning $string until there is no string left
    for($i = 0; $i < $count; $i++)
    {
    
        // $split is array of manipulated string and string to reiterate on
        $split = stringSplitToArray($string);
      
        // manipulated string
        $manipulated[$i] = $split[0];
        // string to manipulate further
        $string = $split[1];
    }
  
}



function stringSplitToArray($string)
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
    if(strpos( $data, 'array(' ) !== false)
    {
        
        // assign splitting criteria
        $condition = 'array(';
        $splitter = ')';
        // $trim is pretty consistent, should be function
        $trim[0] = 'arrray('; // the remainder trims are same
        $trim[1] = "'";
        $trim[2] = " "
        $trim[3] = " \t\n"
        
    }
    elseif(strpos( $data, ',' ) !== false)
    {
        
        // assign splitting criteria
        $condition = ',';
        $splitter = ',';
        $trim[0] = '';
        $trim[1] = "'";
        $trim[2] = " "
        $trim[3] = " \t\n"
        
    }
    elseif(strpos( $data, '=>' ) !== false)
    {
        
        // assign splitting criteria
        $condition = '=>';
        $splitter = '=>';
        $trim[0] = '';
        $trim[1] = "'";
        $trim[2] = " "
        $trim[3] = " \t\n"
        
    }
    
    
    $data = _tcpdfStringSplit($string, $condition, $splitter, $remove, $trim);
    // Now we have the match from above, we can act on it
    // This function will call itself as a way to check each value received
    // i.e. if it encounters an array, ', or =>'
    switch($condition)
    {
        case 'array(':
            // split string at ')', $remove and $trim
            $split = _tcpdfStringSplit();
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

/**
 * _tcpdfStringSplit
 *
 * pass any remove values
 *
 * trim values as array or single value
 *
 *
 **/
function _tcpdfStringSplit( $string, $condition = '', $splitter, $remove = '', $trim = '' )
{
    
    // if condition, split
    if(strlen($condition) > 0)
    {

        $exploded = explode( $splitter, $string, 2);
        
        // string to process further
        $current = $exploded[0];
        
        // string to process later
        $remainder = $exploded[1];
        
        // remove $exploded
        unset($exploded);
        
        // now sasign stuff based on $condition
        switch($condition)
        {
            case 'array(':
                // trim it
                $current = trim($remove);
                
                foreach($trim as $key => $value)
                {
                    
                    $current = trim($current, $value);
                    
                }
                
                
                $current = trim();
                $current = trim();
                break;
                
            case ',':
                //
                
                break;
    
            case '=>':
                //
                
                break;
    
            default:
                
                break;
        }
    }
    else
    {
        
        // string to process later
        $remainder = '';
        
        // string to process further
        $current = $string;
        
    }
    
    
    
/**
 * _tcpdfTrim
 *
 * pass any remove values
 *
 * trim values as array or single value
 *
 *
 **/
function _tcpdfTrim( $string, $trim = '' )
{
    // clean it up
    if((strlen($trim) > 0) || is_array($trim))
    {
        
        foreach($trim as $key => $value)
        {
            
            if(is_string($key))
            {
                
                // trim it, baby
                $key = trim();
            }
        }
    
    }
    
    
    
    

}
