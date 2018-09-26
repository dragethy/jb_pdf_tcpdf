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
        self::_tcpdf($process);

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




    // convert tags to useable data
    // from Header, Footer, Settings
    // @tip: Assign that data to instance i.e. instance->method(param)
    // @tip: If Body, then this has different approach and refer to _tcpdfTagHelperHtml
    protected static function _tcpdfTagHelper( $data, $delimiter )
    {

        if ( $data )
        {
            if ( $data != '' && strpos( $data, '<tcpdf' ) !== false )
            {
                $data = self::_tcpdfTagToData( $data, $data['delimiter'] );

                // pass params to instance
                // $vaue is array array(class => '', method => 'someMethod', params => array(0,1,2...))
                foreach ($data as $key => $value)
                {

                    self::_tcpdfParamsBuilder(&$pdf,&$value['method'], &$value['params']);
                }
            }

        }
    }




    // convert tags to useable data
    // from Body
    // @tip: Any params need to be a php array, not string
    // @tip: If no params then can be left as is
    // @tip: this is because it will be added in $pdf->writeHTML($html, true, 0, true, 0);
    // @example: Leave as is: <tcpdf method="AddPage" />
    // @example: Example of converted: $params = $pdf->serializeTCPDFtagParameters(array(0));
    // @example: $html .= '<tcpdf method="SetDrawColor" params="'.$params.'" />';
    // @tip: need to factor in multidimensional arrays
    protected static function _tcpdfTagHelperHtml( $data, $delimiter )
    {

        if ( $data != '' && strpos( $data, '<tcpdf' ) !== false )
        {
            // TODO
            // create array
            $matches = array();

            // Need to find each occurence of <tcpdf(.*?)/> in the html
            preg_match_all('/<tcpdf method="(.*?)" params="(.*?)"/>/', $data, $matches);

            // within that find /params="(.*?)"/
            foreach ($matches as $key => $value)
            {
                // foreach result in array: [0] is whole string, [1] is first () match, [2] is 2nd () match etc
                // I want the 2nd () match if there is one
                if ($value[2]) {
                    # code...
                    $array  = self::_split($value[2]);
                    // apply  $pdf->serializeTCPDFtagParameters($array);
                    $hashed = $pdf->serializeTCPDFtagParameters($array);
                    // create new <tcpdf... with hashed params
                    $string = str_replace($value[2], $hashed, $value[0]);
                    // place back in to string
                    $data = str_replace($value[0], $string, $data);
                }

            }
        }

        return $data;

    }



    // _tcpdfParamsBuilder
    // <tcpdf class="" method="" params="">);
    // becomes array[0]['method'] = someMethod
    // becomes array[0]['params'] = array(param1,param2,...)
    protected static function _tcpdfTagToData( $string, $delimiter )
    {

        $matches    =   '';

        $array = self::_splitDelimiter($string, $delimiter);

        foreach($array as $k => $v)
        {

            if (preg_match('/method="(.*?)"/', $v, $match) === 1)
            {
               $matches[$k]['method'] = $match[1];
            }

            if (preg_match('/params="(.*?)"/', $v, $match) === 1)
            {
                // split params in to array
                $matches[$k]['params'] = self::_split($match[1]);
            }
        }

        return $matches;
    }




    // _tcpdfParamsBuilder
    protected static function _tcpdfParamsBuilder( &$pdf,&$method, &$param )
    {

        // Parameters 10 max (should be enough, are there any methods that can take more?)
        switch (count($param))
        {
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


    // _tcpdf
    protected static function _tcpdf( $data )
    {

        //  require_once('tcpdf_include.php');
        require_once($data['location_tcpdf']);

        // initiate
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // convert and apply tags $pdf->method($params)
        if ( $data['header'] )
        {
            self::_tcpdfTagHelper($data['header'],$data['delimiter']);

        }
        if ( $data['body'] )
        {
            // TODO
            $data['body'] = self::_tcpdfTagHelperHtml($data['body'],$data['delimiter']);
            $pdf->writeHTML($data['body'], true, 0, true, 0);

        }
        if ( $data['footer'] )
        {
            self::_tcpdfTagHelper($data['footer'],$data['delimiter']);

        }
        if ( $data['settings'] )
        {
            self::_tcpdfTagHelper($data['settings'],$data['delimiter']);

        }

        // create the title for pdf (used in 'save as' option on computer.)
        $pdf->Output($data['name'], $data['destination']);

    }
} // END OF PLUGIN
