<?php

namespace Simple;

class Validator
{
    /**
     * Contains the field names that have errors
     * @var array
     */
    private $_errors = [];

    /**
     * References to data yet to be validated
     * @var array 
     */
    private $_rawData;
    
    /**
     * @param array $data Data to-be-validated
     */
    public function __construct($data)
    {
        $this->_rawData = $data;
    }

    /**
     * Returns number of errors
     * 
     * @return int
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
    public function validate($params) 
    {
        foreach($params as $field => $rules) {
            foreach ($rules as $rule) {
                if ($this->callMethod($field, $rule) === false) {
                    $this->_errors[] = $field;
                    continue 2;
                }
            }
        }
        return $this->countErrors() === 0;
    }  

    /**
     * Calls a validation function and returns the result
     * 
     * @return bool
     */
    private function callMethod($field, $params)
    {
        if (is_array($params)) {
            $rule = array_shift($params);
            $args = $params;
        } else {
            $rule = $params;
            $args = [];
        }

        if ($rule !== 'required' && $rule !== 'accepted' && empty($this->_rawData[$field])) {
            return true;
        }
        // PSR-1 compliant method name
        $method = 'validate' . ucfirst($rule);

        return $this->$method($field, $args);
    }

    /*-------------------------------------------
    | Validation functions below:
    -------------------------------------------*/

    protected function validateRequired($field) 
    {
        return !empty($this->_rawData[$field]);
    }

    protected function validateAccepted($field)    
    {
        $acceptable = array('yes', 'on', 1, '1', true);
        return $this->validateRequired($field) && in_array($this->_rawData[$field], $acceptable, true);
    }

    protected function validateEmail($field)
    {
        return filter_var($this->_rawData[$field], FILTER_VALIDATE_EMAIL);
    }

    /**
     * Takes extra parameter if user wants to ignore spaces
     */
    protected function validateAlpha($field, $params = [0])
    {
        if ($params[0] === 1) {
            $subject = str_replace(' ', '', $this->_rawData[$field]);
        } else {
            $subject = $this->_rawData[$field];
        }

        return ctype_alpha($subject);
    }
    
    protected function validateAlphaNum($field) 
    {
        return ctype_alnum($this->_rawData[$field]);
    }
    
    protected function validateDigit($field) 
    {
        return is_int($this->_rawData[$field]) || ctype_digit($this->_rawData[$field]);
    }
    
    protected function validateLength($field, $params)
    {
        $strlen = strlen($this->_rawData[$field]);
        $length = $params[0] ?? 0;

        return $strlen === $length;
    }

    protected function validateLengthMax($field, $params)
    {
        $strlen = strlen($this->_rawData[$field]);
        $length = $params[0] ?? 0;

        return $strlen <= $length;
    }

    protected function validateLengthBetween($field, $params) 
    {
        $strlen = strlen($this->_rawData[$field]);
        $min = $params[0] ?? 0;
        $max = $params[1] ?? 0;

        return $strlen >= $min && $strlen <= $max;
    }
    
    protected function validateLengthMin($field, $params) 
    {
        $strlen = strlen($this->_rawData[$field]);
        $length = $params[0] ?? 0;

        return $strlen >= $length;
    }

    protected function validateMin($field, $params)
    {
        $min = $params[0] ?? 0;
        return $this->_rawData[$field] >= $min;
    }

    protected function validateMax($field, $params)
    {
        $max = $params[0] ?? 0;
        return $this->_rawData[$field] <= $max;
    }

    protected function validateRegex($field, $params) 
    {
        $regex = $params[0] ?? '';
        return preg_match($regex, $this->_rawData[$field]) === 1;
    }
}
