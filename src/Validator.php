<?php

namespace Simple;

class Validator
{
    /**
     * Contains the errors with field names as keys
     * @var array
     */
    private $_errors = [];

    /**
     * References to data yet to be validated
     * @var array 
     */
    private $_rawData;

    /**
     * Returns only the data that was validated. Because the 
     * original data may contain useless data
     * 
     * @var array
     */
    private $_validData;
    
    /**
     * @param array $data Data to-be-validated
     */
    public function __construct(&$data)
    {
        $this->_rawData = $data;
    }

    /**
     * @return int Number of errors
     */
    public function countErrors()
    {
        return count($this->_errors);
    }

    /**
     * @return array Array of field wise errors
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Main validation function exposed to public
     * 
     * @return Validation $this
     */
    public function validate($params, &$validData = null) 
    {
        foreach($params as $field => $rules) {
            foreach ($rules as $rule) {
                if ($this->callMethod($field, $rule) === false) {
                    continue 2;
                }
            }

            // If a second parameter is provided which references to
            // an array.
            if (is_array($validData)) {
                $validData[$field] = $this->_rawData[$field];
            }
        }
        return $this->countErrors() === 0;
    }  

    /**
     * @return bool
     */
    private function callMethod($field, $rule)
    {
        if (is_array($rule)) {
            $method = 'validate' . ucfirst(array_shift($rule));

            switch (count($rule)) {
                case 1:
                    return $this->$method($field, $rule[0]);
                    break;
                
                case 2:
                    return $this->$method($field, $rule[0], $rule[1]);
                    break;

                default:
                    call_user_func_array([$this, $method], $rule);
                    break;
            }
        } else {
            $method = 'validate' . ucfirst($rule);
            return $this->$method($field);
        }
    }
    
    // Validation functions below:

    protected function validateRequired($field) 
    {
        if (empty($this->_rawData[$field])) {
            $this->_errors[$field] = true;
            return false;
        }
        return true;
    }

    protected function validateEmail($field)
    {
        if (empty($this->_rawData[$field])) {
            return;
        }

        if (!filter_var($this->_rawData[$field], FILTER_VALIDATE_EMAIL)) {
            $this->_errors[$field] = true;            
            return false;
        }
        return true;
    }

    protected function validateAlpha($field, $space = 0)
    {
        if (empty($this->_rawData[$field])) {
            return;
        }

        if ($space === 1) {
            $subject = str_replace(' ', '', $this->_rawData[$field]);
        } else {
            $subject = $this->_rawData[$field];
        }

        if (!ctype_alpha($subject)) {
            $this->_errors[$field] = true;
            return false;
        }
        return true;
    }
    
    protected function validateAlphanum($field) 
    {
        if (empty($this->_rawData[$field])) {
            return;
        }

        if (!ctype_alnum($this->_rawData[$field])) {
            $this->_errors[$field] = true;
            return false;
        }
        return true;
    }
    
    protected function validateDigit($field) 
    {
        if (empty($this->_rawData[$field])) {
            return;
        }

        if (!is_int($this->_rawData[$field]) && !ctype_digit($this->_rawData[$field])) {
            $this->_errors[$field] = true;
            return false;
        }
        return true;
    }
    
    protected function validateMaxlength($field, $length) 
    {
        if (empty($this->_rawData[$field])) {
            return;
        }

        $strlen = strlen($this->_rawData[$field]);
        if ($strlen > $length) {
            $this->_errors[$field] = true;
            return false;
        }
        return true;
    }
    
    protected function validateMinlength($field, $length) 
    {
        if (empty($this->_rawData[$field])) {
            return;
        }

        $strlen = strlen($this->_rawData[$field]);
        if ($strlen < $length) {
            $this->_errors[$field] = true;
            return false;
        }
        return true;
    }

    protected function validateMin($field, $min) 
    {
        if (empty($this->_rawData[$field])) {
            return;
        }

        if ($this->_rawData[$field] < $min) {
            $this->_errors[$field] = true;
            return false;
        }
        return true;
    }

    protected function validateMax($field, $max) 
    {
        if (empty($this->_rawData[$field])) {
            return;
        }

        if ($this->_rawData[$field] > $max) {
            $this->_errors[$field] = true;
            return false;
        }
        return true;
    }
}

?>