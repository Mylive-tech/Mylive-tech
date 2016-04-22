<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 class Phpld_View {

    protected static $view = null;
    protected static $layout = null;

    protected static $_javascripts = null;
    protected static $_stylesheets = null;
    /**
     * @static
     * @return Smarty
     */
    public static function getView()
    {
        if (!is_null(self::$view)) {
            return self::$view;
        }

        self::$view = self::_initView();
        return self::$view;
    }

    /**
     * @static
     * @return Smarty
     */
    public static function getLayout()
    {
        if (!is_null(self::$layout)) {
            return self::$layout;
        }

        self::$layout = self::_initView();
        return self::$layout;
    }

    protected static function _initView()
    {
        if (!empty ($_SESSION['user_language']))
        {
            $language = $_SESSION['user_language'];
        }
        elseif (defined ('LANGUAGE'))
        {
            $language = LANGUAGE;
            if (empty ($language))
                $language = 'en';
        }
        else
            $language = 'en';
        $tpl = new IntSmarty($language);

        //Determine what template to use
        if (empty ($force_tpl)) {
            determine_template();
        } else {
            //define ('USE_TEMPLATE', $force_tpl.'/');
            define ('TEMPLATE', $force_tpl.'/');
        }

        $is_mobile_user = is_mobile_user();

        //$tpl->template_dir = INSTALL_PATH.'templates/'.USE_TEMPLATE;

         if (!$is_mobile_user) {
            $tpl->compile_dir  = INSTALL_PATH.'temp/templates';
            $tpl->cache_dir    = INSTALL_PATH.'temp/cache';
	    $tpl->addTemplateDir(INSTALL_PATH.'templates/'.TEMPLATE);
	    
        } else {
            $tpl->compile_dir  = INSTALL_PATH.'temp/templates_mobile';
            $tpl->cache_dir    = INSTALL_PATH.'temp/cache_mobile';
	    $tpl->addTemplateDir(INSTALL_PATH.'templates/'.MOBILE_TEMPLATE);
	    
        }
	$tpl->addTemplateDir(INSTALL_PATH.'templates/Core/DefaultFrontend');

        return $tpl;
    }

    public static function addJavascript($href)
    {
        self::$_javascripts[] = $href;
    }

    public static function addStylesheet($href)
    {
        self::$_stylesheets[] = $href;
    }

    public static function assignStylesAndScripts()
    {
        if (!is_null(self::$_javascripts)) {
            self::$_javascripts = array_unique(self::$_javascripts);
            $javascripts = null;
            foreach (self::$_javascripts as $script) {
                $javascripts .= '<script type="text/javascript" src="'.$script.'"></script>';
            }
            self::getLayout()->assign('phpldJavascripts', $javascripts);
        }

        if (!is_null(self::$_javascripts)) {
            self::$_stylesheets = array_unique(self::$_stylesheets);
            $stylesheets = null;
            foreach (self::$_stylesheets as $script) {
                $stylesheets .= '<link rel="stylesheet" type="text/css" href="'.$script.'" />';
            }
            self::getLayout()->assign('phpldStylesheets', $stylesheets);
        }

    }
}
