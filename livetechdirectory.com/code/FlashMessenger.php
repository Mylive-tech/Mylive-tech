<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 class Phpld_FlashMessenger
{
    protected $_namespaces = array(
        'info',
        'siccess',
        'warning',
        'error',
    );


    public function fm()
    {
        return $this;
    }

    /**
     * Adds success message
     * @param Array|String $msg
     */
    public function info($msg)
    {
        $this->addMessage('info', $msg);
    }

    /**
     * Adds success message
     * @param Array|String $msg
     */
    public function success($msg)
    {
        $this->addMessage('success', $msg);
    }

    /**
     * Adds error message
     * @param Array|String $msg
     * @return void
     */
    public function error($msg)
    {
        $this->addMessage('error', $msg);
    }

    /**
     * Adds warning message
     * @param Array|String $msg
     * @return void
     */
    public function warning($msg)
    {
        $this->addMessage('warning', $msg);
    }

    public function formValidation($validationResult)
    {
        foreach ($validationResult as $field=>$message) {
            $this->error($field.': '.(is_array($message) ? $message['remote'] : $message));
        }
    }

    public function addMessage($namespace, $message)
    {
        $_SESSION['pld_messages'][$namespace][] = $message;
    }

    /**
     * @param bool $doNotClear If true messages will NOT be removed from session
     * @return mixed
     */
    public function getMessages($doNotClear = false)
    {
        if (isset($_SESSION['pld_messages'])) {
            $messages = $_SESSION['pld_messages'];
            if ($doNotClear == false) {
                unset($_SESSION['pld_messages']);
            }
            return $messages;
        }
    }

    public function assign($tpl, $name = 'FLASH_MESSENGER')
    {
        $tpl->assign($name, $this);
    }

    /**
     * Render messages
     *
     * @param string|int $indent
     * @return string
     */
    public function render()
    {
        $namespaces = $this->getMessages();
        $view = Phpld_View::getView();
        $view->assign('namespaces', $namespaces);
        return $view->fetch('views/_shared/_placeholders/flashMessenger.tpl');

        return $xhtml;
    }

    public function __toString()
    {
        return $this->render();
    }


}
