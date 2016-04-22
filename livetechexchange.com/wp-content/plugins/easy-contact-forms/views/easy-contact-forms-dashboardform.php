<?php
/**
 * @file
 *
 * 	EasyContactFormsDashBoard DashBoard form html template
 *
 * 	@see EasyContactFormsDashBoard::getDashBoardForm()
 */

/*  Copyright Georgiy Vasylyev, 2008-2012 | http://wp-pal.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 * Please read the entire license text in the license.txt file
 */


EasyContactFormsLayout::getFormHeader('ufo-formpage ufo-dashboardform ufo-' . strtolower($obj->type));
echo EasyContactFormsUtils::getViewDescriptionLabel(EasyContactFormsT::get('DashBoard'));
EasyContactFormsLayout::getFormHeader2Body();

?>
  <div>
    <div>
      <div style='width:300px;float:left'>
        <div class='ufo-dashboard-header'>
          <?php echo EasyContactFormsT::get('UserStatistics');?>
        </div>
        <div>
          <?php $obj->getUserStatistics();?>
        </div>
      </div>
      <?php EasyContactFormsLayout::getTabHeader(array('WhatsNew', 'GettingStarted', 'Other'), 'left');?>
      <div class='ufo-tab-wrapper ufo-tab-left'>
        <div id='WhatsNew' class='ufo-tabs ufo-tab ufo-active'>
          <div class='ufo-float-left ufo-width50'>
            <div>
              <img style='margin-left:-20px;margin-top:-10px;' src='<?php echo EASYCONTACTFORMS__engineWebAppDirectory; ?>/forms/images/vcita_bunner.jpg'>
            </div>
          </div>
          <div class='ufo-float-right ufo-width50'>
            <div>
              <div style='font-size:0.9em;height:120px;overflow:auto;padding-right:10px;margin-right:-20px'>
                 <div style='font-weight:bold'>
                   current version: 1.4
                 </div>
                 <ol>
                   <li style='margin:0;padding:1px'>
                     File upload feature is added. A contact form may contain several file upload buttons.
                   </li>
                   <li style='margin:0;padding:1px'>
                     Several admin part improvements
                   </li>
                   <li style='margin:0;padding:1px'>
                     Several front end contact form bugs fixed
                   </li>
                 </ol>
              </div>
            </div>
          </div>
          <div style='clear:left'></div>
        </div>
        <div id='GettingStarted' class='ufo-tabs ufo-tab'>
          <div class='ufo-float-left ufo-width50'>
            <div>
              <label><?php echo EasyContactFormsT::get('FAQSection');?></label>
              <a href='http://easy-contact-forms.com/your-questions/'>Frequently Asked Questions</a>
            </div>
            <div>
              <label><?php echo EasyContactFormsT::get('SupportRequests');?></label>
              <a href='http://easy-contact-forms.com/support/'>Support</a>
            </div>
          </div>
          <div class='ufo-float-right ufo-width50'>
            <div>
              <label><?php echo EasyContactFormsT::get('OurSite');?></label>
              <a href='http://easy-contact-forms.com'>Visit our site for tutorials and more</a>
            </div>
          </div>
          <div style='clear:left'></div>
        </div>
        <div id='Other' class='ufo-tabs ufo-tab'>
          <div class='ufo-float-left ufo-width50'>
            <div>
              <div style='font-size:0.9em;height:120px;overflow:auto;padding-right:10px'>
                 <div style='font-weight:bold'>
                   Easy Contact Forms today
                 </div>
                 <ol style='margin-top:5px'>
                   <li style='margin:0;padding:1px'>
                     More than a year of development
                   </li>
                   <li style='margin:0;padding:1px'>
                     Tons of nice fieatures
                   </li>
                   <li style='margin:0;padding:1px'>
                     Friendly interface
                   </li>
                   <li style='margin:0;padding:1px'>
                     Cutting edge techologies
                   </li>
                 </ol>
              </div>
            </div>
          </div>
          <div style='clear:left'></div>
        </div>
      </div>
    </div>
    <div>
      <div class='ufo-dashboard-header'>
        <?php echo EasyContactFormsT::get('FormStatistics');?>
      </div>
      <div>
        <?php $obj->getFormStatistics();?>
      </div>
      <div class='ufo-dashboard-header'>
        <?php echo EasyContactFormsT::get('EntryStatistics');?>
      </div>
      <div>
        <?php $obj->getEntryStatistics();?>
      </div>
    </div>
  </div><?php

EasyContactFormsLayout::getFormBodyFooter();
