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
* @version          JB Pdf Tcpdf
*
*
*
**/

defined( '_JEXEC' ) or die;
$options2   =   JCckDev::fromJSON( $this->item->options2 );
$to_admin   =   ( is_array( @$options2['to_admin'] ) ) ? implode( ',', $options2['to_admin'] ) : ( ( @$options2['to_admin'] ) ? $options2['to_admin'] : '' );
?>

<div class="seblod well">
    <?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$this->item->type.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php

        /*
        *
        * create_select
        * create_field
        * create_field_trigger
        *
        * @options: Never, Always, Add, Edit
        * @tip: Similar to Seblod's Email Field
        * @tip: create_field overrides "Never", i.e. use a select field with yes=1 no=0
        * @tip: create_field_trigger references the trigger for create_field
        * @example: create_field references select field called "orientation" which has the options "Landscape=landscape||Portrait=portrait".
        * @example: create_field_trigger set as "landscape" as it's value, so when "orientation" equals "landscape", the pdf plugin is triggered.
        * @example: you could then have another pdf plugin with create_field_trigger set as "portrait".
        *
        */

        echo '<label>Create PDF</label>';
        echo '<select id="json_options2_create_select" name="json[options2][create_select]" class="inputbox select has-value">
                  <option value="0" selected="selected">Never</option>
                  <option value="3">Always</option>
                  <optgroup label="Workflow">
                      <option value="1">Add</option>
                      <option value="2">Edit</option>
                  </optgroup>
              </select>';
        echo '<input type="text" id="json_options2_create_field" name="json[options2][create_field]" value="" class="inputbox text" placeholder="some_field_to_override_never" size="50" maxlength="255" />';
        echo '<input type="text" id="json_options2_create_field_trigger" name="json[options2][create_field_trigger]" value="" class="inputbox text" placeholder="If field, what value triggers this plugin" size="50" maxlength="255" />';




        /*
        *
        * name
        *
        * @options: Text [$user,$uri,$fields,#field_name#]
        * @tip:
        * @example: "some/folder/$user->id/somepdf.php"
        *
        */

        echo '<label>Name PDF</label>';
        echo '<input type="text" id="json_options2_name" name="json[options2][name]" value="" class="inputbox text" placeholder="/some/folder/$user-&rsaquo;id/mypdf.pdf" size="50" maxlength="255" />';





        /*
        *
        * destination
        * @options: I,D,F,S,FI,FD,E
        *
        * @tip: I: send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.
        * @tip: D: send to the browser and force a file download with the name given by name.
        * @tip: F: save to a local server file with the name given by name.
        * @tip: S: return the document as a string (name is ignored).
        * @tip: FI: equivalent to F + I option
        * @tip: FD: equivalent to F + D option
        * @tip: E: return the document as base64 mime multi-part email attachment (RFC 2045)
        * @example: "F"
        *
        */

        echo '<label>Destination</label>';
        echo '<select id="json_options2_destination" name="json[options2][destination]" class="inputbox select has-value">
                  <option value="I" selected="selected">I</option>
                  <option value="D">D</option>
                  <option value="F">F</option>
                  <option value=S"">S</option>
                  <option value="FI">FI</option>
                  <option value="FD">FD</option>
                  <option value="E">E</option>
              </select>';



        /*
        *
        * tcpdf
        *
        * @options: Text
        * @tip: Enter location and name of tcpdf.php file
        * @example: /libraries/tcpdf/tcpdf.php
        *
        */
        echo '<label>Location TCPDF</label>';
        echo '<input type="text" id="json_options2_location_tcpdf" name="json[options2][location_tcpdf]" value="" class="inputbox text" placeholder="location/of/tcpdf.php" size="50" maxlength="255" />';





        /*
        *
        * settings
        *
        * @options: Add your method name and value using a html style tag
        * @example: <tcpdf method="addPageBreak" value="true,10" class="">
        * @tip: add any method, and reference the class if not default value
        * @tip: add as many as you like, these will be initiated before the rendering of the pdf
        * @tip: any method can be added in to the document, these will be applied as they appear, good for when requiring a specific page break
        *
        */

        echo '<label>TCPDF Settings</label>';
        echo '<textarea id="json_options2_settings" name="json[options2][settings]" value="" class="inputbox text" placeholder="&lsaquo;tcpdf method=&ldquo;addPageBreak&rdquo; params=&ldquo;true,10&rdquo; &frasl;&rsaquo;" col="100" rows="10" ></textarea>';



        /*
        *
        * Header
        *
        * @tip: same functionality as Seblod's Email Message field
        * @tip: Accepts [$user,$uri,$fields,#field_name#]
        *
        * @example: <tcpdf  method="SetHeaderData" params="PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING" />
        * @example: output as $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
        *
        */
        echo '<label>Header</label>';
        echo '<textarea id="json_options2_header" name="json[options2][header]" value="" class="inputbox text" placeholder="&lsaquo;tcpdf  method=&ldquo;SetHeaderData&rdquo; params=&ldquo;PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.\' 006\', PDF_HEADER_STRING&rdquo; &frasl;&rsaquo;" col="100" rows="10" ></textarea>';


        /*
        *
        * Body
        *
        * @tip: same functionality as Seblod's Email Message field
        * @tip: Accepts [$user,$uri,$fields,#field_name#]
        *
        */
        echo '<label>Body</label>';
        echo '<textarea id="json_options2_body" name="json[options2][body]" value="" placeholder="#some_field# &lsaquo;tcpdf  method=&ldquo;SetHeaderData&rdquo; params=&ldquo;PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.\' 006\', PDF_HEADER_STRING&rdquo; &frasl;&rsaquo;" class="inputbox text" col="100" rows="10"></textarea>';



        /*
        *
        * Footer
        *
        * @tip: same functionality as Seblod's Email Message field
        * @tip: Accepts [$user,$uri,$fields,#field_name#]
        *
        */
        echo '<label>Footer</label>';
        echo '<textarea id="json_options2_footer" name="json[options2][footer]" value="" class="inputbox text" placeholder="&lsaquo;tcpdf  method=&ldquo;SetFooterMargin&rdquo; params=&ldquo;PDF_MARGIN_FOOTER&rdquo; &frasl;&rsaquo;" col="100" rows="10" ></textarea>';



        // Add link to tutorial on forum i.e. https://www.seblod.com/community/forums/fields-plug-ins/pdf-plugin
        // RenderHelp forces a dodgy link so need to hardcode or do something else
        echo JCckDev::renderHelp( 'field', 'pdf-plugin' );
        echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
        echo JCckDev::getForm( 'core_storage', $this->item->storage, $config );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // If Never is selected, then show override field
    //$('#json_options2_create_field').isVisibleWhen('json_options2_create_select','0',true,'visibility');
    //$('#json_options2_create_field_trigger').isVisibleWhen('json_options2_create_select','0',true,'visibility');
});
</script>
