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
    public function validate($params, &$validData = null) 
    {
        foreach($params as $field => $rules) {
            foreach ($rules as $r) {
                if ($this->callMethod($field, $rule) === false) {
                    $this->_errors[$field] = true;
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
     * Calls a validation function and returns the result
     * 
     * @return bool
     */
    private function callMethod($field, $params)
    {
        if (is_array($rule)) {
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
    
    protected function validateLengthMax($field, $params)
    {
        $strlen = strlen($this->_rawData[$field]);
        $length = $params[0] ?? 0;

        return $strlen <= $length;
    }

    protected function validateLengthBetween($field, $params) 
    {
        return $this->validateLengthMin($field, $min) && $this->validateLengthMax($field, $max);
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

    protected function validateRegex($field, $regex) 
    {
        return preg_match($regex, $this->_rawData[$field]) === 1;
    }
}
