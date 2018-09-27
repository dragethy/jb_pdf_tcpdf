<?php
/**
* @version          SEBLOD 3.x Core
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
class plgCCK_FieldJbPdfTcpdf extends JCckPluginField
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

        $isNew      =   ( $config['pk'] ) ? 0 : 1;

        // Determine whether we create PDF or not?
        $create_select  =   ( isset( $options2['create_select'] ) && $field->state != 'disabled' ) ? $options2['create_select'] : 0;
        $create_field = ( isset( $options2['create_field'] ) && $field->state != 'disabled' ) ? $options2['create_field'] : '';
        $create_field_trigger   =   ( isset( $options2['create_field_trigger'] ) && $field->state != 'disabled' ) ? $options2['create_field_trigger'] : '';
        $name   =   ( isset( $options2['name'] ) && $field->state != 'disabled' ) ? $options2['name'] : '';
        $destination   =   ( isset( $options2['destination'] ) && $field->state != 'disabled' ) ? $options2['destination'] : JPATH_SITE.'/'.'images/mypdf.pdf';
        $location_tcpdf   =   ( isset( $options2['location_tcpdf'] ) ) ? $options2['location_tcpdf'] : JPATH_SITE.'/'.'libraries'.'/'.'tcpdf'.'/'.'tcpdf.php';
        $delimiter   =   ( isset( $options2['delimiter'] ) ) ? $options2['delimiter'] : '';
        $settings   =   ( isset( $options2['settings'] ) ) ? $options2['settings'] : '';
        $header =   ( isset( $options2['header'] ) ) ? $options2['header'] : '';
        $body   =   ( isset( $options2['body'] ) ) ? $options2['body'] : '';
        $footer =   ( isset( $options2['footer'] ) ) ? $options2['footer'] : '';

        $valid      =   0;

        // Prepare
        switch ( $create ) {
            case 0:
            $create_field_trigger = ($create_field_trigger == '') ? 1 : $create_field_trigger;
              $valid = ($fields[$create_field]->value == $create_field_trigger) ? 1 : 0;
                break;

            case 1:
                $valid  =   ($isNew === 1) ? 1 : 0;
                break;

            case 2:
                $valid  =   ($isNew === 0) ? 1 : 0;
                break;

            case 3:
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
                'name'=>$name,
                'destination'=>$destination,
                'location_tcpdf'=>$location_tcpdf,
                'delimiter'=>$delimiter,
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
        $isNew      =   $process['isNew'];
        $valid      =   $process['valid'];

        if ( $valid )
        {

        }
        if ( !$valid )
        {
            return;
        }

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




    // _split($strng, 'delimiter')
    protected static function _splitDelimiter( $string, $delimiter = ',' )
    {

        $tab    =   explode( $delimiter, $string );

        return $tab;
    }




    /*
    *
    * str_replace with correct values from Joomla and Seblod
    * $type = user, fields, config, uri
    * $body = copied straight from seblod code, they used body, where as I would have probably gone with $data
    * @example:
    * @return = $data with updated values
    *
    */
    protected static function _tcpdfStrReplaceVariables( $type = '', $body)
    {

        switch ($type)
        {
            case 'user':
                # code...
                if ( $body != '' && strpos( $body, '$user->' ) !== false ) {
                    $user           =   JCck::getUser();
                    $matches        =   '';
                    $search         =   '#\$user\->([a-zA-Z0-9_]*)#';
                    preg_match_all( $search, $body, $matches );
                    if ( count( $matches[1] ) ) {
                        foreach ( $matches[1] as $k=>$v ) {
                            $body   =   str_replace( $matches[0][$k], $user->$v, $body );
                        }
                    }
                }

                break;

            case 'fields':
                break;

            case 'config':
                break;

            case 'uri':
                break;

            default:
                # code...
                break;
        }
















            $subject    =   str_replace( '[id]', $config['id'], $subject );
            $subject    =   str_replace( '[pk]', $config['pk'], $subject );
            $subject    =   str_replace( '[sitename]', $config2->get( 'sitename' ), $subject );
            $subject    =   str_replace( '[siteurl]', JUri::base(), $subject );

            // J(translate) for subject
            if ( $subject != '' && strpos( $subject, 'J(' ) !== false ) {
                $matches    =   '';
                $search     =   '#J\((.*)\)#U';
                preg_match_all( $search, $subject, $matches );
                if ( count( $matches[1] ) ) {
                    foreach ( $matches[1] as $text ) {
                        $subject    =   str_replace( 'J('.$text.')', JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) ), $subject );
                    }
                }
            }

            if ( isset( $config['registration_activation'] ) ) {
                $body       =   str_replace( '[activation]', JUri::root().'index.php?option=com_users&task=registration.activate&token='.$config['registration_activation'], $body );
                $body       =   str_replace( '[username]', $fields['username']->value, $body );
                $subject    =   str_replace( '[username]', $fields['username']->value, $subject );
            }
            // {del fieldname}{/del}
            if ( $body != '' && strpos( $body, '{del' ) !== false ) {
                $dels   =   null;
                $body = str_replace( "\n", "", $body );
                preg_match_all( '#\{del ([^\{]*)\}([^\{]*)\{\/del\}#', $body, $dels );
                for ( $i = 0, $n = count( $dels[1] ); $i <= $n; $i++ ) {
                    $match  =   str_replace( '#', '' ,$dels[1][$i] );
                    if ( isset( $fields[$match]->value ) && trim( $fields[$match]->value ) ){
                        $body   =   str_replace( $dels[0][$i], $dels[2][$i], $body );
                    } else {
                        $body   =   str_replace( $dels[0][$i], '', $body );
                    }
                }
            }
            // #fieldnames#
            $matches    =   null;
            preg_match_all( '#\#([a-zA-Z0-9_]*)\##U', $body, $matches );
            if ( count( $matches[1] ) ) {
                foreach ( $matches[1] as $match ) {
                    if ( trim( $match ) && isset( $fields[$match]->text ) && trim( $fields[$match]->text != '' ) ) {
                        $body   =   str_replace( '#'.$match.'#', $fields[$match]->text, $body );
                    } else {
                        $body   =   ( trim( $match ) && isset( $fields[$match]->value ) && trim( $fields[$match]->value ) ) ? str_replace( '#'.$match.'#', $fields[$match]->value, $body ) : str_replace( '#'.$match.'#', '', $body );
                    }
                }
            }
            $matches    =   null;
            preg_match_all( '#\#([a-zA-Z0-9_]*)\##U', $subject, $matches );
            if ( count( $matches[1] ) ) {
                foreach ( $matches[1] as $match ) {
                    if ( trim( $match ) && isset( $fields[$match]->text ) && trim( $fields[$match]->text ) != '' ) {
                        $subject    =   str_replace( '#'.$match.'#', $fields[$match]->text, $subject );
                    } else {
                        $subject    =   ( trim( $match ) && isset( $fields[$match]->value ) && trim( $fields[$match]->value ) != '' ) ? str_replace( '#'.$match.'#', $fields[$match]->value, $subject ) : str_replace( '#'.$match.'#', '', $subject );
                    }
                }
            }

            // $cck->getAttr('fieldname');
            if ( $body != '' && strpos( $body, '$cck->get' ) !== false ) {
                $matches    =   '';
                $search     =   '#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_]*)\' ?\)(;)?#';
                preg_match_all( $search, $body, $matches );
                if ( count( $matches[1] ) ) {
                    for ( $i = 0, $n = count( $matches[1] ); $i <= $n; $i++ ) {
                        $attr   =   strtolower( $matches[1][$i] );
                        $match  =   $matches[2][$i];
                        if ( isset( $fields[$match]->$attr ) && trim( $fields[$match]->$attr ) != '' ){
                            $body   =   str_replace( $matches[0][$i], $fields[$match]->$attr, $body );
                        } else {
                            $body   =   str_replace( $matches[0][$i], '', $body );
                        }
                    }
                }
            }

            // J(translate)
            if ( $body != '' && strpos( $body, 'J(' ) !== false ) {
                $matches    =   '';
                $search     =   '#J\((.*)\)#U';
                preg_match_all( $search, $body, $matches );
                if ( count( $matches[1] ) ) {
                    foreach ( $matches[1] as $text ) {
                        $body   =   str_replace( 'J('.$text.')', JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) ), $body );
                    }
                }
            }

            $body       =   str_replace( '[id]', $config['id'], $body );
            $body       =   str_replace( '[pk]', $config['pk'], $body );
            $body       =   str_replace( '[sitename]', $config2->get( 'sitename' ), $body );
            $body       =   str_replace( '[siteurl]', JUri::base(), $body );
            if ( $body != '' && strpos( $body, '$user->' ) !== false ) {
                $user           =   JCck::getUser();
                $matches        =   '';
                $search         =   '#\$user\->([a-zA-Z0-9_]*)#';
                preg_match_all( $search, $body, $matches );
                if ( count( $matches[1] ) ) {
                    foreach ( $matches[1] as $k=>$v ) {
                        $body   =   str_replace( $matches[0][$k], $user->$v, $body );
                    }
                }
            }
            // [date(.*)]
            if ( $body != '' && strpos( $body, '[date' ) !== false ) {
                $matches    =   null;
                preg_match_all( '#\[date(.*)\]#U', $body, $matches );
                if ( count( $matches[1] ) ) {
                    foreach ( $matches[1] as $match ) {
                        $date       =   date( $match );
                        $body       =   str_replace( '[date'.$match.']', $date, $body );
                    }
                }
            }
            // [fields]
            if ( strpos( $body, '[fields]' ) !== false ) {
                $bodyF  =   null;
                if ( count( $fields ) ) {
                    foreach ( $fields as $field ) {
                        $fieldName  =   $field->name;
                        if ( ! ( $field->type == 'password' && $field->value == 'XXXX' ) && isset( $field->value ) && trim( $field->value ) != '' && ( $field->variation != 'hidden' ) ) {
                            $valF   =   ( isset( $field->text ) && trim( $field->text ) != '' ) ? trim( $field->text ) : trim( $field->value );
                            $bodyF  .=  '- '.$field->label.' : '.$valF.'<br /><br />';
                        }
                    }
                }
                $body   =   ( strpos( $body, '[fields]' ) !== false ) ? str_replace( '[fields]', $bodyF, $body ) : $body.substr( $bodyF, 0, -12 );
            }
























    }




    /*
    *
    * get method and params from any tag
    * serialize them if going back in to the html, default is 'no' (0).
    *
    * @tip: Body uses different approach
    *
    * @example:
    * @return: array(0=>array($string,1st match in string, 2nd match string)
    *
    */
    protected static function _tcpdfGetMethodParams( $pdf, $data, $serialized = 0)
    {

        if ( $data )
        {

            if ( $data != '' && strpos( $data, '<tcpdf' ) !== false )
            {

                // make an array of the strings method and params
                preg_match_all('/<tcpdf[\s]?method="(.*?)"[\s]?params="(.*?)"[\s]?\/>/', $data, $matches);

            }

            // get params as array, and serialized if needed
            foreach ($matches[0] as $key => $value)
            {

                $matches['params'][$key] = self::_split($matches[2][$key]);

                if ($serialized === 1)
                {

                    $matches['params'][$key] = $pdf->serializeTCPDFtagParameters($matches['params'][$key]);

                }

            }


        }

        return $matches;

    }



    /*
    *
    * $pdf = instance
    * $matches = matches array from _tcpdfGetMethodParams
    * $serialized = boolean
    * $data = $data['body'] etc required if serializing
    *
    * @tip: set method and params from $data array
    * @tip: if serialized === 1 then it is going to be a string replace in html
    * @tip: array() is data from _tcpdfGetMethodParams
    *
    * @tip: Some methods may not require params : <tcpdf method="AddPage" />
    *
    *
    */

    protected static function _tcpdfSetMethodParams( $pdf, $matches = array(), $serialized = 0, $data )
    {

        if ( is_array($matches) )
        {

            // set the method and params
            foreach ($matches[0] as $key => $value)
            {

                if ($serialized === 0)
                {
                    // $pdf->method($params)
                    // get the method
                    $method = $matches[1][$key];

                    $params = $matches[2][$key];

                    // if not serialized then pass to instance and call the funky method with the funky parameters
                    self::_tcpdfSetPdfMethodParams(&$pdf,$method,$params);

                }
                elseif ($serialized === 1)
                {

                    // search for original string in $data and replace with '<tcpdf method=".$method." params=".$params." />';
                    // reconstruct string with serialized params
                    $search = $matches[0][$key];
                    $replace = '<tcpdf method="'.$matches[1][$key].'" params="'.$matches['params'][$key].'" />';
                    $subject = $data;

                    $data = str_replace($search, $replace, $subject);

                    $pdf->writeHTML($data, true, 0, true, 0);

                }


            }

        }

        return $data;
    }




    // _tcpdfCallMethod
    protected static function _tcpdfSetPdfMethodParams( &$pdf,&$method, &$params = '' )
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
                $pdf->$method($param[0]);
                break;
        }

    }





    /*
    *
    * $data = Seblod's $process
    *
    **/

    protected static function _tcpdfHelper( $data )
    {

        //  require_once('tcpdf_include.php');
        require_once($data['location_tcpdf']);

        // initiate
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // str_replace Seblod stuff
        if ( $data['settings'] )
        {

            $$data['settings'] = self::_tcpdfStrReplaceVariables('user', $data['settings']);
            $$data['settings'] = self::_tcpdfStrReplaceVariables('fields', $data['settings']);
            $$data['settings'] = self::_tcpdfStrReplaceVariables('config', $data['settings']);
            $$data['settings'] = self::_tcpdfStrReplaceVariables('uri', $data['settings']);

        }

        if ( $data['header'] )
        {

            $array = self::_tcpdfGetMethodParams($pdf, $data['header']);
            $data['header'] = self::_tcpdfSetMethodParams(&$pdf,$array);

        }

        if ( $data['body'] )
        {

            $array = self::_tcpdfGetMethodParams($pdf, $data['body'], 1);
            $data['body'] = self::_tcpdfSetMethodParams(&$pdf, $array, 1, $data['body']);

        }

        if ( $data['footer'] )
        {

            $array = self::_tcpdfGetMethodParams($pdf, $data['footer']);
            $data['footer'] = self::_tcpdfSetMethodParams(&$pdf,$array);

        }




        // get method and params from <tcpdf> tag and apply as $pdf->method($params) or serialized
        if ( $data['settings'] )
        {


            $array = self::_tcpdfGetMethodParams($pdf, $data['settings']);
            $data['settings'] = self::_tcpdfSetMethodParams(&$pdf,$array);

        }

        if ( $data['header'] )
        {

            $array = self::_tcpdfGetMethodParams($pdf, $data['header']);
            $data['header'] = self::_tcpdfSetMethodParams(&$pdf,$array);

        }

        if ( $data['body'] )
        {

            $array = self::_tcpdfGetMethodParams($pdf, $data['body'], 1);
            $data['body'] = self::_tcpdfSetMethodParams(&$pdf, $array, 1, $data['body']);

        }

        if ( $data['footer'] )
        {

            $array = self::_tcpdfGetMethodParams($pdf, $data['footer']);
            $data['footer'] = self::_tcpdfSetMethodParams(&$pdf,$array);

        }

        // create the title for pdf (used in 'save as' option on computer.)
        $pdf->Output($data['name'], $data['destination']);

    }
} // END OF PLUGIN
