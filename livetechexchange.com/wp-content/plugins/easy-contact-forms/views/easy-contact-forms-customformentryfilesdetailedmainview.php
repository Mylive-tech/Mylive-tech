<?php
/**
 * @file
 *
 * 	EasyContactFormsCustomFormEntryFiles detailedMain view html template
 *
 * 	@see EasyContactFormsCustomFormEntryFiles ::getDetailedMainView()
 */

/*  Copyright Georgiy Vasylyev, 2008-2012 | http://wp-pal.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 * Please read the entire license text in the license.txt file
 */

?>
  <div>
    <div class='buttons'>
      <div class='ufo-float-left'>
        <?php EasyContactFormsIHTML::getScroller($obj);?>
      </div>
      <div class='ufo-float-left'>
        <?php echo EasyContactFormsIHTML::getButton(
          array(
            'title' => EasyContactFormsT::get('Delete'),
            'events' => " onclick='ufo.mdelete($obj->jsconfig, $obj->mdeleteconfig)'",
            'iclass' => " class='icon_button_delete' ",
            'bclass' => "ufo-imagebutton",
          )
        );?>
      </div>
      <div style='clear:left'></div>
    </div>
  </div>
  <div>
    <div id='divCustomFormEntryFilesFilter' class='ufo-filter'>
      <div class='ufofilterbutton'>
        <?php echo EasyContactFormsIHTML::getButton(
          array(
            'label' => EasyContactFormsT::get('Filter'),
            'events' => " onclick='ufo.filter($obj->jsconfig);'",
            'iclass' => " class='icon_filter_pane' ",
            'bclass' => "button internalimage",
          )
        );?>
      </div>
      <div class='ufo-clear-both'></div>
      <div>
        <div></div>
      </div>
    </div>
  </div>
  <div>
    <div class='viewtable'>
      <table class='vtable'>
        <tr>
          <th style='width:8px'>
            <input type='checkbox' class='ufo-id-link' style='margin:0' onchange='ufo.checkAll(this)'>
          </th>
          <th style='width:30px'>
            <?php EasyContactFormsIHTML::getColumnHeader(array('view' => $obj, 'field' => "id"));?>
          </th>
          <th>
            <?php EasyContactFormsIHTML::getColumnHeader(
              array(
                 'view' => $obj,
                 'field' => "Description",
                 'label' => EasyContactFormsT::get('Field'),
              )
            );?>
          </th>
          <th>
            <?php echo EasyContactFormsT::get('File');?>
          </th>
        </tr>
        <?php EasyContactFormsLayout::getRows(
          $resultset,
          'EasyContactFormsCustomFormEntryFiles',
          $obj,
          'easy-contact-forms-customformentryfilesdetailedmainviewrow.php',
          'getCustomFormEntryFilesDetailedMainViewRow',
          $viewmap
        );?>
      </table>
    </div>
  </div>
