<?php
/**
* @version          SEBLOD 3.x TCPDF
* @package          SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url              https://www.seblod.com
* @editor           Octopoos - www.octopoos.com
* @copyright        Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license          GNU General Public License version 2 or later; see _LICENSE.php
**/

/**
* @version          JB Pdf
*
*
*
**/

defined( '_JEXEC' ) or die;
// Plugin
class plgCCK_FieldJb_Pdf_Tcpdf extends JCckPluginField
{
    protected static $type      =   'jb_pdf_tcpdf';
    protected static $path;

    // -------- -------- -------- -------- -------- -------- -------- -------- // Construct

    // onCCK_FieldConstruct
    public function onCCK_FieldConstruct( $type, &$data = array() )
    {
        if ( self::$type != $type ) {
            return;
        }
        parent::g_onCCK_FieldConstruct( $data );

    }

  // -------- -------- -------- -------- -------- -------- -------- -------- // Prepare

    // onCCK_FieldPrepareContent
    public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
    {
        if ( self::$type != $field->type ) {
            return;
        }
        parent::g_onCCK_FieldPrepareContent( $field, $config );

        $field->value   =   $value;
    }


    // onCCK_FieldPrepareForm
    public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
    {
        if ( self::$type != $field->type ) {
            return;
        }
        self::$path =   parent::g_getPath( self::$type.'/' );
        parent::g_onCCK_FieldPrepareForm( $field, $config );

        // Init
        if ( count( $inherit ) ) {
            $id     =   ( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
            $name   =   ( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
        } else {
            $id     =   $field->name;
            $name   =   $field->name;
        }
        $value      =   ( $value != '' ) ? $value : $field->defaultvalue;
        $value      =   ( $value != ' ' ) ? $value : '';
        $value      =   str_replace(array( '"','\\' ), '', $value );
        $value      =   htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' );

        // Validate
        $validate   =   '';
        if ( $config['doValidation'] > 1 ) {
            plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
            parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
            $validate   =   ( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
        }

        // Prepare
        $class  =   'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
        $maxlen =   ( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
        $attr   =   'class="'.$class.'" size="'.$field->size.'"'.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
        $form   =   '<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';

        // Set
        if ( ! $field->variation ) {
            $field->form    =   $form;
            if ( $field->script ) {
                parent::g_addScriptDeclaration( $field->script );
            }
        } else {
            parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
        }
        $field->value   =   $value;

        // Return
        if ( $return === true ) {
            return $field;
        }
    }


    // onCCK_FieldPrepareSearch
    public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
    {
        if ( self::$type != $field->type ) {
            return;
        }

        // Prepare
        self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );

        // Return
        if ( $return === true ) {
            return $field;
        }
    }


    // onCCK_FieldPrepareStore
    public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
    {

        if ( self::$type != $field->type ) {
            return;
        }
        // Init
        if ( count( $inherit ) ) {
            $name   =   ( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
        } else {
            $name   =   $field->name;
        }

        $options2   =   JCckDev::fromJSON( $field->options2 );

        // based on email 'send'
        // first, is it Never=0||Add=1||Edit=2||Always=3
        $create_select  =   ( isset( $options2['create_select'] ) && $field->state != 'disabled' ) ? $options2['create_select'] : 0;
        // is 'never' overriden by a field?
        $create_field = ( isset( $options2['create_field'] ) && strlen( $options2['create_field'] ) > 0 ) ? $options2['create_field'] : 0;
        // what value from that field is the trigger, default is 1
        $create_field_trigger   =   ( isset( $options2['create_field_trigger'] ) && strlen( $options2['create_field_trigger'] ) > 0 ) ? $options2['create_field_trigger'] : 1;
        // name_pdf i.e. some/folder/mypdf.pdf
        $name_pdf =   ( isset( $options2['name_pdf'] ) && strlen( $options2['name_pdf'] ) > 0 ) ? $options2['name_pdf'] : JPATH_SITE.'/'.'images/mypdf.pdf';
        //  where to send i.e to browser etc https://www.rubydoc.info/gems/rfpdf/1.17.1/TCPDF:Output
        $destination_pdf   =   ( isset( $options2['destination_pdf'] ) && strlen( $options2['destination_pdf'] ) > 0 ) ? $options2['destination_pdf'] : 'F';
        // where the tcpdf stuff is
        $name_tcpdf   =   ( isset( $options2['name_tcpdf'] ) && strlen( $options2['name_tcpdf'] ) > 0 ) ? $options2['name_tcpdf'] : JPATH_SITE.'/'.'libraries'.'/'.'TCPDF-master'.'/'.'tcpdf.php';
        // split strings by this value, might be redundant now
        $settings = ( isset( $options2['settings'] ) && strlen( $options2['settings'] ) > 0 ) ? $options2['settings'] : '';
        $header = ( isset( $options2['header'] ) && strlen( $options2['header'] ) > 0 ) ? $options2['header'] : '';
        $body = ( isset( $options2['body'] ) && strlen( $options2['body'] ) > 0 ) ? $options2['body'] : '';
        $footer = ( isset( $options2['footer'] ) && strlen( $options2['footer'] ) > 0 ) ? $options2['footer'] : '';

        // value to store in $db
        $value = $body;
        $isNew      =   ( $config['pk'] ) ? 0 : 1;

        $valid      =   0;

        // Prepare
        switch ( $create_select ) {
            case 0:
                // if fields value and trigger value are the same then go for it else do not
                $valid = ($fields[$create_field]->value == $create_field_trigger) ? 1 : 0;
                break;

            case 1:
                // if add...
                $valid  =   ($isNew === 1) ? 1 : 0;
                break;

            case 2:
                // if edit
                $valid  =   ($isNew === 0) ? 1 : 0;
                break;

            case 3:
                // er... always...
                $valid  =   1;
                break;

            default:
                $valid = 0;
                break;
        }



        // Validate
        parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );

        // Add Process
        if ( $valid ) {
            parent::g_addProcess( 'afterStore', self::$type, $config, array(
                'isNew'=>$isNew,
                'create_select'=>$create_select,
                'create_field'=>$create_field,
                'create_field_trigger'=>$create_field_trigger,
                'name_pdf'=>$name_pdf,
                'destination_pdf'=>$destination_pdf,
                'name_tcpdf'=>$name_tcpdf,
                'settings'=>$settings,
                'header'=>$header,
                'body'=>$body,
                'footer'=>$footer,
                'valid'=>$valid
            ));
        }
        // Set or Return
        if ( $return === true ) {
            return $value;
        }
        $field->value   =   $value;
        parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
    }

    // -------- -------- -------- -------- -------- -------- -------- -------- // Render

    // onCCK_FieldRenderContent
    public static function onCCK_FieldRenderContent( $field, &$config = array() )
    {
        return parent::g_onCCK_FieldRenderContent( $field );
    }

    // onCCK_FieldRenderForm
    public static function onCCK_FieldRenderForm( $field, &$config = array() )
    {
        return parent::g_onCCK_FieldRenderForm( $field );
    }



    // -------- -------- -------- -------- -------- -------- -------- -------- // Special Events

    // onCCK_FieldAfterStore
    public static function onCCK_FieldAfterStore( $process, &$fields, &$storages, &$config = array() )
    {


        $valid      =   $process['valid'];

        if ( !$valid )
        {
            return;
        }

        $isNew      =   $process['isNew'];


        // str_replace Seblod stuff
        // $storages and $config are TODO, but first I need to provide 'enable' optiion in field settings

        if ( $process['name_pdf'] )
        {
            $process['name_pdf'] = self::_tcpdfSetDynamicValues($process['name_pdf'], $fields, $config );
        }

        if ( $process['destination_pdf'] )
        {
            $process['destination_pdf'] = self::_tcpdfSetDynamicValues($process['destination_pdf'], $fields, $config );
        }

        if ( $process['settings'] )
        {
            $process['settings'] = self::_tcpdfSetDynamicValues($process['settings'], $fields, $config );
        }

        if ( $process['header'] )
        {
            $process['header'] = self::_tcpdfSetDynamicValues($process['header'], $fields, $config );
        }

        if ( $process['body'] )
        {
            $process['body'] = self::_tcpdfSetDynamicValues($process['body'], $fields, $config );
        }

        if ( $process['footer'] )
        {
            $process['footer'] = self::_tcpdfSetDynamicValues($process['footer'], $fields, $config );
        }


        // TODO: Might be good to update content in DB with updates values
        // $content = JCckContent::getInstance($config[pk]);
        // $content->set('field', $body)
        // ->store();
        // create pdf
        self::_tcpdfHelper($process);

    }





    // -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script

    // _split
    protected static function _split( $string )
    {
        $string     =   str_replace( array( ' ', "\r" ), '', $string );
        if ( strpos( $string, ',' ) !== false ) {
            $tab    =   explode( ',', $string );
        } else if ( strpos( $string, ';' ) !== false ) {
            $tab    =   explode( ';', $string );
        } else {
            $tab    =   explode( "\n", $string );
        }

        return $tab;
    }





    /*
    *
    * $body = Seblod's $process['setting'] etc
    * $config = Seblod's $config
    *
    *
    *
    * str_replace with correct values from Joomla and Seblod
    * $type = user, fields, config, uri
    * $body = copied straight from seblod code, they used body, where as I would have probably gone with $data or even $process
    * @example:
    * @return = $data with updated values
    *
    */
    protected static function _tcpdfSetDynamicValues( &$body, &$fields, &$config = array() )
    {

        // $config2
        // Seblod use $config2 for Joomla Config stuff as opposed to Seblod $config stuff
        $config2    =   JFactory::getConfig();



        // J(translate)
        // NO WORK
        // if ( $body != '' && strpos( $body, 'J(' ) !== false )
        // {
        //     $matches    =   '';
        //     $search     =   '#J\((.*)\)#U';
        //     preg_match_all( $search, $body, $matches );
        //     if ( count( $matches[1] ) )
        //     {
        //         foreach ( $matches[1] as $text )
        //         {
        //             $body    =   str_replace( 'J('.$text.')', JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) ), $body );
        //         }
        //     }
        // }


        // $user
        // WORKS
        if ( $body != '' && strpos( $body, '$user->' ) !== false )
        {
            $user           =   JCck::getUser();
            $matches        =   '';
            $search         =   '#\$user\->([a-zA-Z0-9_]*)#';
            preg_match_all( $search, $body, $matches );
            if ( count( $matches[1] ) )
            {
                foreach ( $matches[1] as $k=>$v )
                {
                    $body   =   str_replace( $matches[0][$k], $user->$v, $body );
                }
            }

        }


        // [date(.*)]
        // DOES WORK KINDA, NOT SURE HOW TO USE IT
        if ( $body != '' && strpos( $body, '[date' ) !== false )
        {
            $matches    =   null;
            preg_match_all( '#\[date(.*)\]#U', $body, $matches );
            if ( count( $matches[1] ) )
            {
                foreach ( $matches[1] as $match )
                {
                    $date       =   date( $match );
                    $body       =   str_replace( '[date'.$match.']', $date, $body );
                }
            }
        }




        // #fieldnames#
        // WORKS
        $matches    =   null;
        preg_match_all( '#\#([a-zA-Z0-9_]*)\##U', $body, $matches );
        if ( count( $matches[1] ) )
        {
            foreach ( $matches[1] as $match )
            {
                if ( trim( $match ) && isset( $fields[$match]->text ) && trim( $fields[$match]->text != '' ) )
                {
                    $body   =   str_replace( '#'.$match.'#', $fields[$match]->text, $body );
                } else {
                    $body   =   ( trim( $match ) && isset( $fields[$match]->value ) && trim( $fields[$match]->value ) ) ? str_replace( '#'.$match.'#', $fields[$match]->value, $body ) : str_replace( '#'.$match.'#', '', $body );
                }
            }
        }



        // {del fieldname}{/del}
        // NOT TESTED
        if ( $body != '' && strpos( $body, '{del' ) !== false )
        {
            $dels   =   null;
            $body = str_replace( "\n", "", $body );
            preg_match_all( '#\{del ([^\{]*)\}([^\{]*)\{\/del\}#', $body, $dels );
            for ( $i = 0, $n = count( $dels[1] ); $i <= $n; $i++ )
            {
                $match  =   str_replace( '#', '' ,$dels[1][$i] );
                if ( isset( $fields[$match]->value ) && trim( $fields[$match]->value ) )
                {
                    $body   =   str_replace( $dels[0][$i], $dels[2][$i], $body );
                } else {
                    $body   =   str_replace( $dels[0][$i], '', $body );
                }
            }
        }




        // $cck->getAttr('fieldname');
        // WORKS
        if ( $body != '' && strpos( $body, '$cck->get' ) !== false )
        {
            $matches    =   '';
            $search     =   '#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_]*)\' ?\)(;)?#';
            preg_match_all( $search, $body, $matches );

            if ( count( $matches[1] ) )
            {
                foreach ( $matches[1] as $k=>$v )
                {

                    $attr   =   strtolower( $v );
                    $match  =   $matches[2][$k];

                    if ( isset( $fields[$match]->$attr ) && trim( $fields[$match]->$attr ) != '' )
                    {
                        $match = $fields[$match]->$attr;

                        $body  =   str_replace( $matches[0][$k], $match, $body );
                    }

                }
            }
        }





        // $uri;
        // WORKS
        if ( $body != '' && strpos( $body, '$uri->' ) !== false )
        {

            $app        =   JFactory::getApplication();
            $matches    =   '';
            $search     =   '#\$uri\-> ?\'?([a-zA-Z0-9_]*)\'? ?(;)?#';
            preg_match_all( $search, $body, $matches );
            if ( count( $matches[1] ) )
            {
                foreach ( $matches[1] as $k=>$v )
                {
                    $match = $app->input->get( $v, '' );
                    $body       =   str_replace( $matches[0][$k], $match, $body );

                }
            }
        }



        // $config
        // TODO
        // Haven't got a scoobey-doo, should I even do it? If so, I really need an option to enable it




        // [id][pk][sitename][siteurl]
        // WORKS
        $body   =   str_replace( '[id]', $config['id'], $body );
        $body   =   str_replace( '[pk]', $config['pk'], $body );
        $body   =   str_replace( '[sitename]', $config2->get( 'sitename' ), $body );
        $body   =   str_replace( '[siteurl]', JUri::base(), $body );

        return $body;

    }










    /*
    *
    * $string = string of arrays, can be nested
    *
    * @tip: get string and convert to array
    * @tip: there might be nested arrays
    *
    * @return: assoc array();
    *
    */
    protected static function _tcpdfGetArrayFromString($string)
    {

        // return this array
        $data = array();

        // count qty of commas in $string. If 1 or more then it is an array
        // $count = substr_count($string, ',');
        // count qty of letters in $string
        $count = strlen($string);

        // if comma then break in to an array else return it
        if ($count)
        {

            // It will either be
            // a) "array(1,2,3 => 4),2,3,4"
            // b) "1,2,array(1,2,3 => 4)"
            // separate from end of array [0] is params, [1] is remainder of string]
            for ($i=0; $i < $count; $i++)
            {


                if ( (strpos($string, 'array(') === 0) || (strpos($string, ',') !== false))
                {
                    
                    // start of string = 'array(' or contains ','...
                    if ( strpos($string, 'array(') === 0 )
                    // if ((strpos($string, 'array(') === 0) || (strpos($string, ',') !== false))
                    {
    
                        // $pos = strpos( $value, ')' );
                        $delimiter = ')';
                        $trimmer[0] = 'array(';
                        $trimmer[1] = ',';
                        
                    }
                    elseif(strpos($string, ',') !== false)
                    {
    
                            // if does not start with array, split at first ",".
                            $delimiter = ',';
                            $trimmer[0] = '';
                            $trimmer[1] = ',';
                            
                    }

                    $exploded = explode($delimiter, $string, 2);
                    // tidy up
                    $part = ltrim($exploded[0], $trimmer[0]);
                    $remainder = ltrim($exploded[1], $trimmer[1]);
    
                    // do not need $exploded anymore
                    unset($exploded);

                    // split $part has comma then it is array, else not array
                    if (strpos($part, ',') !== false)
                    {
                        
                        $part = explode(',', $part);
                        
                    }
                    
                    
                    
                    // if $part is an array, and some of the values are meant to be assoc array i.e. cat => 'dog'...
                    if(is_array($part))
                    {
                        
                        foreach ($part as $key => $value)
                        {
                            // if assoc assign $key and $value accordingly
                            if (strpos( $value, '=>' ))
                            {
    
                                $exploded = explode('=>', $value);
    
                                $key = trim($exploded[0]);
                                $value = trim($exploded[1]);
                            }
    
                            $array[$key] = $value;
    
                        }
                        
                        // reassign $part with update keys
                        $part = $array;
                        
                        unset($array);
                        
                    }

                    // reset string in loop as the remaining substring
                    $string = $remainder;
                    
                }
                else
                {
                    
                    // if no commas then nothing to do except assing the value to $data[$i]
                    $part = $string;
                    
                    // close the forloop
                    $i = $count;
                    
                }

                 
                $data[$i] = $part;
        
            }
             
            // all done
            return $data;
             
        }


    }







    /*
    *
    * get method, params and types from ::tcpdf method="" prarams="" types="" /::
    * serialize them if going back in to the html, default is 'no' (0).
    *
    * @tip: Body uses different approach
    * @tip: If serialized, then you need the html ($data) to do string replace with
    *
    * @example:
    * @return: [0] array of strings that have matched the regexp
    * @return: [1] array of params for each match
    * @return: [2] array of types for the param
    *
    */
    // protected static function _tcpdfGetMethodParamsTypes( &$pdf, $data, $serialized = 0)
    protected static function _tcpdfGetMethodParamsTypes( &$pdf, $data)
    {
      
        $matches = array();

        if ( $data )
        {

            if ( $data != '' && strpos( $data, 'tcpdf' ) !== false )
            {

                // make an array of  [0]match [1]method [2]params [3]types
                // [^\"] tells it to get everything except ", so it will stop when it finds one
                preg_match_all('/tcpdf[\s]?method="[\s]?([^\"]*?)[\s]?"[\s]?params="[\s]?([^\"]*?)[\s]?" types="[\s]?([^\"]*?)[\s]?"/', $data, $matches);


                // pass each params as string of arrays, return params as array of arrays
                foreach ($matches[2] as $key => $value)
                {

                    // get params as array of values
                    $matches[2][$key] = self::_tcpdfGetArrayFromString($matches[2][$key]);

                }


                // pass each types as string of arrays, return types as array of arrays
                foreach ($matches[3] as $key => $value)
                {

                    // get types as array of values
                    $matches[3][$key] = self::_tcpdfGetArrayFromString($matches[3][$key]);

                }

            }

        }


        return $matches;

    }






    /*
    *
    * $pdf = instance
    * $matches = matches array from _tcpdfGetMethodParamsTypes
    * $serialized = boolean
    * $data = $data['body'] etc required if serializing
    *
    * @tip: set method and params from $data array
    * @tip: if serialized === 1 then it is going to be a string replace in html
    * @tip: array() is data from _tcpdfGetMethodParamsTypes
    *
    * @tip: Some methods may not require params : tcpdf method="AddPage" />
    *
    *
    */

    protected static function _tcpdfSetMethodParamsTypes( &$pdf, $matches = array(), $serialized = 0, &$data = array() )
    {

        if ( is_array($matches) )
        {

            // set the method and params
            foreach ($matches[0] as $key => $value)
            {

                // $pdf->method($params)
                // get the method
                $method = $matches[1][$key];

                $params = $matches[2][$key];

                // set type
                foreach ($variable as $key => $value) {
                    # code...
                }
                if ($serialized === 0)
                {

                    // if not serialized then pass to instance and call the funky method with the funky parameters
                    self::_tcpdfSetPdfMethodParams($pdf,$method,$params);

                }
                elseif ($serialized === 1)
                {

                    // search for original string in $data and replace with 'tcpdf method=".$method." params=".$params." />';
                    // reconstruct string with serialized params
                    $search = $matches[0][$key];
                    $replace = 'tcpdf method="'.$method.'" params="'.$params.'"';
                    $subject = $data;

                    $data = str_replace($search, $replace, $subject);

                    $pdf->writeHTML($data);

                }

            }

        }

        return $data;
    }




    // _tcpdfCallMethod
    protected static function _tcpdfSetPdfMethodParams( &$pdf,&$method, &$params = array() )
    {


        // Parameters 10 max (should be enough, are there any methods that can take more?)
        switch (count($param))
        {
            case 0:
                $pdf->$method();
                break;
            case 1:
                $pdf->$method($param[0]);
                break;
            case 2:
                $pdf->$method($param[0],$param[1]);
                break;
            case 3:
                $pdf->$method($param[0],$param[1],$param[2]);
                break;
            case 4:
                $pdf->$method($param[0],$param[1],$param[2],$param[3]);
                break;
            case 5:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4]);
                break;
            case 6:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5]);
                break;
            case 7:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6]);
                break;
            case 8:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7]);
                break;
            case 9:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8]);
                break;
            case 10:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9]);
                break;
            case 11:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9],$param[10]);
                break;
            case 12:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9],$param[10],$param[11]);
                break;
            case 13:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9],$param[10],$param[11],$param[12]);
                break;
            case 14:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9],$param[10],$param[11],$param[12],$param[13]);
                break;
            case 15:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9],$param[10],$param[11],$param[12],$param[13],$param[14]);
                break;
            case 16:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9],$param[10],$param[11],$param[12],$param[13],$param[14],$param[15]);
                break;
            case 17:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9],$param[10],$param[11],$param[12],$param[13],$param[14],$param[15],$param[16]);
                break;
            case 18:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9],$param[10],$param[11],$param[12],$param[13],$param[14],$param[15],$param[16],$param[17]);
                break;
            case 19:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9],$param[10],$param[11],$param[12],$param[13],$param[14],$param[15],$param[16],$param[17],$param[18]);
                break;
            case 20:
                $pdf->$method($param[0],$param[1],$param[2],$param[3],$param[4],$param[5],$param[6],$param[7],$param[8],$param[9],$param[10],$param[11],$param[12],$param[13],$param[14],$param[15],$param[16],$param[17],$param[18],$param[19]);
                break;

            default:
                $pdf->$method();
                break;
        }

    }


    /*
    *
    * $data = Seblod's $process
    *
    * @tip: important to have settings last as it needs header and footer data set first or something like that
    *
    **/

    protected static function _tcpdfHelper( &$data )
    {



        //  require_once('tcpdf_include.php');
        require_once($data['name_tcpdf']);

        // initiate
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // get data from tcpdf method="" params="" types="", and apply as $pdf->method((type) $params) or serialize data and str_replace it
        if ( $data['header'] )
        {
            $array = self::_tcpdfGetMethodParamsTypes($pdf, $data['header']);
            $data['header'] = self::_tcpdfSetMethodParamsTypes($pdf,$array);

        }

        if ( $data['footer'] )
        {

            $array = self::_tcpdfGetMethodParamsTypes($pdf, $data['footer']);
            $data['footer'] = self::_tcpdfSetMethodParamsTypes($pdf,$array);

        }


        if ( $data['settings'] )
        {

            $array = self::_tcpdfGetMethodParamsTypes($pdf, $data['settings']);
            $data['settings'] = self::_tcpdfSetMethodParamsTypes($pdf,$array);

        }

        if ( $data['body'] )
        {

            $array = self::_tcpdfGetMethodParamsTypes($pdf, $data['body']);
            $data['body'] = self::_tcpdfSetMethodParamsTypes($pdf, $array, 1, $data['body']);

        }

        // create the title for pdf (used in 'save as' option on computer.)
$message = $pdf->getFooterMargin();
JFactory::getApplication()->enqueueMessage($message , '$output');
        $pdf->Output($data['name_pdf'], $data['destination_pdf']);

    }
} // END OF PLUGIN












