<?php

require_once(INSTALL_PATH."/libs/Smarty3/SmartyBC.class.php");
require_once(INSTALL_PATH."/libs/Smarty3/Smarty.class.php");

/**
 * IntSmarty is an extension on the PHP Smarty templating library
 * found at <A href="http://smarty.php.net/">smarty.php.net</A>. It
 * is designed to allow the easy implementation of localization
 * by harnessing the power of the Smarty templating language for
 * translations between languages.
 *
 * This class is (c) John Coggeshall, all rights reserved. It may be
 * used in accordance to the Coggeshall.org license available at:
 *
 * <http://www.coggeshall.org/oss/license.php>
 *
 * Or by e-mailing the author at <john@coggeshall.org>
 *
 * For the latest version of IntSmarty, visit:
 *
 * <http://www.coggeshall.org/oss/intsmarty/>
 *
 * @author John Coggeshall
 * @version 0.9
 * @since Smarty 2.5.3
 */

/**
 * This function contains the functionality for the block-level {l}{/l} tags used in IntSmarty templates
 * to provide the localization functionality. It is registered during the construction of an IntSmarty
 * object.
 *
 * @param string $content The content of the template before it is compiled
 * @param object $smarty A reference to the active Smarty class
 * @return string The original template with all of the translations in place
 */
class IntSmarty extends SmartyBC {

    public $default_lang = "en";
    public $lang_path    = "lang/";
    public $version       = "0.9";

    public $a_languages;
    public $cur_language;
    public $translation = null;
    public $translation_size = false;

    public $transtable_updated;


    /**
     * PHP 5 compatiable destructor
     */
    function __destruct()
    {
        $this->saveLanguageTable();

    }

    /**
     * The IntSmarty constructor, called when a new instance
     * is created.
     *
     * @param string $lang The default language to assume when translating or
     *                     omit to auto-detect
     */
    function __construct($lang = NULL) {

        parent::__construct();
        $this->lang_path = INSTALL_PATH.(preg_match ('#/$#i', INSTALL_PATH) ? '' : '/').'lang/';


        $this->translation = array();
        $this->translation_size = 0;

        $this->register_function("i18nfile", "smarty_function_i18nfile");

        $this->a_languages = array_unique($this->_determineLangs());

        if(! is_null($lang)) {
            array_unshift($this->a_languages , $lang);
        }

//        if(isset( $this->a_languages[0])) {
//            $this->cur_language = $this->a_languages[0];
//        } else {
//            $this->cur_language = NULL;
//        }
        $this->compile_id = $this->cur_language;
        foreach( $this->a_languages as $lang) {
            if( $this->loadLanguageTable($lang)) {
                if ($lang == 'en') {
                    //var_dump($this->a_languages);
                    //var_dump(debug_backtrace());die();
                }
                //var_dump($lang, $this->a_languages);
                $this->cur_language = $lang;
                break;
            } else {
                var_dump("{$this->lang_path}$lang.php");
                var_dump($lang);
            }
        }
//var_dump($this->cur_language);
        $this->loadFilter('pre', "translate");

    }


    /**
     * Used to determine if the provided language is one IntSmarty
     * has translation tables for or not.
     *
     * @param string $lang The language code to check
     * @return bool A boolean indicating if a language table exists for the given language
     */
    function isAvailableLanguage($lang) {
        return in_array($lang, $this->a_languages);
    }

    /**
     * A overridden method from the Smarty class used to hook into the Smarty
     * engine. See the Smarty documentation for more information
     */
	public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
    //function fetch($file, $cache_id = NULL, $compile_id = "", $parent = null, $display = NULL) {
	{
        $cid = $this->compile_id .$compile_id;
        return parent::fetch($template, $cache_id, $cid, $parent);

    }

    /**
     * A private method used to auto-resolve the accepted language for the
     * browser by looking at the $_SERVER['HTTP_ACCEPT_LANGUAGE'] variable.
     * This value can be overridden by passing a language to the constructor
     * as well.
     *
     * @return array An array of the accepted languages, the primary one first.
     */
    function _determineLangs() {

        @preg_match_all("/([a-z\-]*)?[,;]+/i",
            $_SERVER['HTTP_ACCEPT_LANGUAGE'],
            $matches);

        return (count($matches[1])) ? $matches[1] : array($this->default_lang);

    }

    /**
     * This method is called to load the language table for a given language. It
     * is designed to be overriden in such a way that the default filesystem storage
     * can be substituted for a database query, etc. It is responsible for
     * setting the translation table, current language, translation size, etc. for
     * the application.
     *
     * @param string $language The language code to load the table for
     * @return bool A boolean indicating if the table was loaded successfully
     */
    function loadLanguageTable($language) {

        if (!empty($this->translation)) {
            return true;
        }
        $filename = "{$this->lang_path}$language.php";
        if( file_exists($filename)) {
            @require( $filename);
            if( isset($__LANG) and is_array($__LANG)) {
                $this->translation = $__LANG;
                $this->translation_size = count($this->translation);
                $this->assign("lang", $this->cur_language );

                $this->transtable_updated = false;

                return true;

            } else {
                //var_dump($__LANG);
                //die($filename);
            }
        }
        return true;
    }

    /**
     * This method is used to save the active language table for later use
     * in a translation. It is designed to be overriden by an extending class
     * to allow saving to a database, etc.
     *
     * @return bool A boolean indicating if the table was saved successfully
     */
    function saveLanguageTable() {

        if( count($this->translation ) != $this->translation_size) {

            $filename = "{$this->lang_path}{$this->cur_language}.php" ;

            $code = '<?php'."\n";

            if (function_exists ('language_file_data'))
            {
                $langInfo = language_file_data($filename);

                $code .= '/**'."\n";
                $code .= 'Language:'.(!empty ($langInfo['LANGUAGE']) ? ' '.trim ($langInfo['LANGUAGE']) : '')."\n";
                $code .= 'Language File Author:'.(!empty ($langInfo['AUTHOR_NAME']) ? ' '.trim ($langInfo['AUTHOR_NAME']) : '')."\n";
                $code .= 'Language File Author URI:'.(!empty ($langInfo['AUTHOR_URL']) ? ' '.trim ($langInfo['AUTHOR_URL']) : '')."\n";
                $code .= '*/'."\n\n";
            }

            $code .= '$__LANG = '.var_export ($this->translation, true).';'."\n".'?>';
            $fr = @ fopen ($filename, "w");

            if (! $fr) {
                return false;
            }

            @ fputs ($fr, $code);
            @ fclose ($fr);

        }

        return true;
    }

    /**
     * Provides a PHP-side facility to translate strings through the IntSmarty system.
     * Identical to the block IntSmarty tag {l}{/l}, it takes the provided string
     * and attempts to find a suitable translation for the current language.
     *
     * @param string $value The string to translate
     * @return string The translated version of the string for the current language,
     *         or the same string passed to the method if no translation was
     *         found.
     */
    function translate($value)
    {
        if( is_string($value)) {

            $hash = md5($value);

            if( array_key_exists($hash, $this->translation )) {

                return $this->translation[$hash];

            }  else {
                $this->transtable_updated = true;
                $this->translation[$hash] = $value;
                return $value;
            }

        }

    }

    /**
     * This method is a Localized version of the Smarty assign() method, which
     * will assign a value to a variable using its translated form rather than
     * the original language.
     *
     * @param string $var The name of the Template variable being assigned
     * @param string $value The new value of the given template variable
     */
    function assignLang($var, $value = NULL) {

        $this->assign($var, $this->translate($value));

    }

    /**
     * The _compile_source() method is a private method in the Smarty class (version 2.6.x)
     * which has been overriden to allow us to provide access to the Internationalization tables
     * during the compilation of a new Smarty template. It should never be called directly.
     *
     * Please see the Smarty documentation for more information.
     */
    function _compile_source($resource_name, &$source_content, &$compiled_content, $cache_include_path=null) {

        if (file_exists(SMARTY_DIR . $this->compiler_file )) {

            require_once( SMARTY_DIR . $this->compiler_file );

        } else {

            // use include_path

            require_once($this->compiler_file);

        }

        $smarty_compiler = new $this->compiler_class;

        $smarty_compiler->template_dir       = $this->template_dir;
        $smarty_compiler->compile_dir        = $this->compile_dir;
        $smarty_compiler->plugins_dir        = $this->plugins_dir;
        $smarty_compiler->config_dir         = $this->config_dir;
        $smarty_compiler->force_compile      = $this->force_compile;
        $smarty_compiler->caching            = $this->caching;
        $smarty_compiler->php_handling       = $this->php_handling;
        $smarty_compiler->left_delimiter    = $this->left_delimiter;
        $smarty_compiler->right_delimiter   = $this->right_delimiter;
        $smarty_compiler->_version           = $this->_version;
        $smarty_compiler->security           = $this->security;
        $smarty_compiler->secure_dir         = $this->secure_dir;
        $smarty_compiler->security_settings = $this->security_settings;
        $smarty_compiler->trusted_dir        = $this->trusted_dir;
        $smarty_compiler->_reg_objects       = &$this->_reg_objects;
        $smarty_compiler->_plugins           = &$this->_plugins;
        $smarty_compiler->_tpl_vars          = &$this->_tpl_vars;
        $smarty_compiler->default_modifiers = $this->default_modifiers;
        $smarty_compiler->compile_id         = $this->_compile_id;
        $smarty_compiler->_config            = $this->_config;
        $smarty_compiler->request_use_auto_globals  = $this->request_use_auto_globals;
        //$smarty_compiler->parent_inst        = &$this;

        $smarty_compiler->_cache_serial = null;
        $smarty_compiler->_cache_include = $cache_include_path;

        $_results = $smarty_compiler->_compile_file($resource_name, $source_content, $compiled_content);

        if ($smarty_compiler->_cache_serial) {

            $this->_cache_include_info = array(

                'cache_serial'=>$smarty_compiler->_cache_serial,
                'plugins_code'=>$smarty_compiler->_plugins_code,
                'include_file_path' => $cache_include_path);

        } else {

            $this->_cache_include_info = null;

        }

        return $_results;

    }

    /**
     * The __compile_template() method is a private method in the Smarty class (version 2.5.x)
     * which must be hooked to allow IntSmarty to translate documents at compile time. This
     * method is identical to the _compile_source() method in Smarty 2.6.x which IntSmarty
     * also overrides.
     */
    function _compile_template($tpl_file, $template_source, &$template_compiled) {

        if( file_exists(SMARTY_DIR.$this->compiler_file)) {
            require_once SMARTY_DIR.$this->compiler_file;
        } else {
            // use include_path
            require_once $this->compiler_file;
        }

        $smarty_compiler = new $this->compiler_class;

        $smarty_compiler->template_dir       = $this->template_dir;
        $smarty_compiler->compile_dir        = $this->compile_dir;
        $smarty_compiler->plugins_dir        = $this->plugins_dir;
        $smarty_compiler->config_dir         = $this->config_dir;
        $smarty_compiler->force_compile      = $this->force_compile;
        $smarty_compiler->caching            = $this->caching;
        $smarty_compiler->php_handling       = $this->php_handling;
        $smarty_compiler->left_delimiter    = $this->left_delimiter;
        $smarty_compiler->right_delimiter   = $this->right_delimiter;
        $smarty_compiler->_version           = $this->_version;
        $smarty_compiler->security           = $this->security;
        $smarty_compiler->secure_dir         = $this->secure_dir;
        $smarty_compiler->security_settings = $this->security_settings;
        $smarty_compiler->trusted_dir        = $this->trusted_dir;
        $smarty_compiler->_reg_objects       = &$this->_reg_objects;
        $smarty_compiler->_plugins           = &$this->_plugins;
        $smarty_compiler->_tpl_vars          = &$this->_tpl_vars;
        $smarty_compiler->default_modifiers = $this->default_modifiers;
        $smarty_compiler->compile_id         = $this->_compile_id;
        $smarty_compiler->parent_inst        = &$this;

        if ($smarty_compiler->_compile_file($tpl_file, $template_source, $template_compiled)) {
            return true;
        } else {
            $this->trigger_error($smarty_compiler->_error_msg);
            return false;
        }

    }

}


/**
 * This function is mapped directly to the {i18nfile} template function provided by
 * the IntSmarty class. The purpose of this function is to provide a standard method
 * of storing Localized images or other files which must be served without having
 * to have such information in the template itself.
 *
 * The {i18nfile} template function itself accepts  two parameters, 'file' (the
 * filename being resolved), and 'lang' (the language to resolve the file to). If
 * the language is not provided, the default current language will be used.
 *
 * @param string $params The parameters provided to the i18nfile function
 * @param object $smarty the IntSmarty instance
 * @return string The localized path of the file
 */
function smarty_function_i18nfile($params, &$smarty) {

    if(empty( $params['file'])) {
        $smarty->trigger_error("i18nfile: missing 'file' parameter");
        return;
    }

    $filepath = $params['file'];
    $filename = basename($filepath);
    $path = dirname($filepath);

    if(isset( $params['lang'])) {
        $language = $params['lang'];
    } else {
        $language = $smarty->cur_language;
    }

    $newfile = $path . DIR_SEP . $language . DIR_SEP . $filename;
    return $newfile;
}


function smarty_prefilter_translate($content, Smarty_Internal_Template $smarty) {
    
    $inst = &$smarty->smarty;
    //var_dump($inst);die();
    $ldq = preg_quote($inst->left_delimiter, '!');
    $rdq = preg_quote($inst->right_delimiter, '!');

    /* Grab all of the tagged strings */
    $match = array();
    preg_match_all("!{$ldq}l{$rdq}(.*?){$ldq}/l{$rdq}!s", $content, $match, PREG_PATTERN_ORDER);

    foreach( $match[1] as $str) {

        $q_str = preg_quote($str);
        $hash = md5($str);

        /* Do we have a translation for this string? */
//var_dump($inst->translation);
        if(array_key_exists($hash, $inst->translation)) {
            /* Replace all occurances of this string with its translation */
            $content = preg_replace("!{$ldq}l{$rdq}$q_str{$ldq}/l{$rdq}!s",
                $inst->translation [$hash], $content);

        }  else {

            $inst->transtable_updated = true;
            $inst->translation[$hash] = $str;

        }


    }

    /* Strip off the tags now that the strings have been replaced */
    $content = preg_replace("!{$ldq}l{$rdq}(.*?){$ldq}/l{$rdq}!s",
        "\\1", $content);

    return $content;

}