<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 
 
 
 
 
 
 
	
class Phpld_Model_Entity implements ArrayAccess
{
    /**
     * @var Phpld_Model_Abstract
     */
    protected $_model;

    /**
     * Response options
     *
     * @var string|array
     */
    protected $_options = null;

    protected $_modelClass = null;

    /**
     * @param array $options
     */
    public function __construct($options = array(), $model = null)
    {
        $this->setOptions($options);
        if (is_null($model) && !is_null($this->_modelClass)) {
            $classname = $this->_modelClass;
            $this->_model = new $classname();
        } else {
            $this->_model = $model;
        }
    }

    /**
     * Set form state from options array
     *
     * @param  array $options
     * @return Zend_Form
     */
    public function setOptions(array $options)
    {
        $forbidden = array();

        foreach ($options as $key => $value) {
            $normalized = ucfirst($key);
            if (in_array($normalized, $forbidden)) {
                continue;
            }

            $method = 'set' . $normalized;
			//Alexandru Pisarenco: Since __call is defined, this is no longer needed
            //if (method_exists($this, $method)) {
                $this->$method($value);
            /*} else {
                $this->setOption($key, $value);
            }*/
        }
        return $this;
    }

    /**
     * Sets single option value
     * Deprecated
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function setOption($key, $value)
    {
		//Alexandru Pisarenco: Since __call is defined, this is no longer needed, but if it's used, reroute request
		$method = 'set'.$key;
		$this->$method($value);
		trigger_error("Deprecated function called 'setOption'.", E_USER_NOTICE);
        //$this->_options[$key] = $value;
    }


	/**
	 * Automatic "magic" function, which directs all requests to unknown methods here.
	 * Currently handles "get" and "set" prefixes to operate with _options.
	 * @param $name string Automatic function name
	 * @param $arguments array Automatic numerated array of arguments
	 * @return mixed null if unsuccessfull, or get or set value
	 */
	public function __call($name, $arguments)
	{
		if(strlen($name)>3)
		{
			$method = substr($name,0,3);
			$option = substr($name,3);
			//$option_normalized = ucfirst($option);
			switch(strtolower($method))
			{
				case 'get':
					if(isset($this->_options[$option]))
						return $this->_options[$option];
					return null;
				case 'set':
					$this->_options[$option]=$arguments[0];
					return $arguments[0];
			}
		}
		trigger_error("Method '$name' not defined", E_USER_ERROR);
		return null;
	}

	/**
	 * Gets an array of options, a copy of _options
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	}

    /**
     * Set the value at given offset
     *
     * @param mixed $offset offset
     * @param mixed $value  value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->_options[$offset] = $value;
    }

    /**
     * Checks if offset exists
     *
     * @param mixed $offset offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->_options[$offset]);
    }

    /**
     * Unsets the element at given offset
     *
     * @param mixed $offset offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_options[$offset]);
    }

    /**
     * Returns the value at given offset
     *
     * @param mixed $offset offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->_options[$offset];
    }
}