{capture name='field_required'}<span class="errForm">{escapejs}{l}This field is required{/l}{/escapejs}</span>{/capture}
{capture name='invalid_username'}<span class="errForm">{escapejs}{l}Invalid username. Please see the field help for more details.{/l}{/escapejs}</span>{/capture}
{capture name='invalid_email'}<span class="errForm">{escapejs}{l}Invalid email address format{/l}{/escapejs}</span>{/capture}
{capture name='invalid_password'}<span class="errForm">{escapejs}{l}Invalid password. Please see the field help for more details.{/l}{/escapejs}</span>{/capture}
{capture name='password_not_match'}<span class="errForm">{escapejs}{l}Password confirmation does not match. Please type again.{/l}{/escapejs}</span>{/capture}

{capture name=form_error}
{strip}
{if $form_error eq 'INSTALL_ERROR_CONNECT'}
   <div class="error">
      <p>{escapejs}{l}An error occured while connecting to the database. Please check your database username and password.{/l}{/escapejs}</p>
      <p>{escapejs}{l}If you require help with your database server settings, please consult your hosting company.{/l}{/escapejs}</p>
      {if !empty($sql_error)}
         <p>{escapejs}{l}The database server returned the following message:{/l}{/escapejs}</p>
         <p>{$sql_error}</p>
      {/if}
   </div>
{elseif $form_error eq 'INSTALL_ERROR_CREATE_DB'}
   <div class="error">
      <p>{escapejs}{l}An error occured while creating the database. Please check if you have database create rights.{/l}{/escapejs}</p>
      <p>{escapejs}{l}If you require help with your database server settings, please consult your hosting company.{/l}{/escapejs}</p>
      {if !empty($sql_error)}
         <p>{escapejs}{l}The database server returned the following message:{/l}{/escapejs}</p>
         <p>{$sql_error}</p>
      {/if}
   </div>
{elseif $form_error eq 'INSTALL_ERROR_CREATE'}
   <div class="error">
      <p>{escapejs}{l}An error occured while creating/updating the database structure. Please check if you have appropriate database rights.{/l}{/escapejs}</p>
      <p>{escapejs}{l}If you require help with your database server settings, please consult your hosting company.{/l}{/escapejs}</p>
      {if !empty($sql_error)}
         <p>{escapejs}{l}The database server returned the following message:{/l}{/escapejs}</p>
         <p>{$sql_error}</p>
      {/if}
   </div>
{elseif $form_error eq 'SQL_ERROR_ADMIN'}
   <div class="error">
      <p>{escapejs}{l}An error occured while creating the administrative user.{/l}{/escapejs}</p>
      <p>{escapejs}{l}The database server returned the following message:{/l}{/escapejs}</p>
      {if !empty($sql_error)}
         <p>{escapejs}{l}The database server returned the following message:{/l}{/escapejs}</p>
         <p>{$sql_error}</p>
      {/if}
   </div>
{elseif $form_error eq 'ADMIN_REQUIRED'}
   <div class="error">
      <p>{escapejs}{l}No administrative user was found in the database.{/l}{/escapejs}</p>
      <p>{escapejs}{l}You must create an administrative user, otherwise the application would be unusable.{/l}{/escapejs}</p>
   </div>
{elseif $form_error eq 'CONFIG_NOT_FOUND'}
   <div class="error">
      <p>{escapejs}{l}Config file was not found.{/l}{/escapejs}</p>
   </div>
{elseif $form_error eq 'CONFIG_NOT_WRITABLE'}
   <div class="error">
      <p>{escapejs}{l}Config file is not writable.{/l}{/escapejs}</p>
   </div>
{/if}
{/strip}
{/capture}

{if $message ne ''}
{capture name=message}
{strip}
   <div class="success">
      {if $message eq 'INSTALL_DB_CREATED'}
         <p>{escapejs}{l}Database was created succesfully.{/l}{/escapejs}</p>
      {elseif $message eq 'INSTALL_DB_UPDATED'}
         <p>{escapejs}{l}Database was updated succesfully.{/l}{/escapejs}</p>
      {elseif $message eq 'ADMIN_CREATED'}
         <p>{escapejs}{l}Administrative user was created/updated succesfully.{/l}{/escapejs}</p>
      {/if}
   </div>
{/strip}
{/capture}
{/if}