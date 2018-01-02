<?php

namespace Simple;

class Validator
{
    /**
     * Contains the field names that have errors
     * @var array
     */
    private $errors = [];

    /**
     * References to raw data
     * @var array 
     */
    private $rawData;
    
    /**
     * __construct()
     */
    public function __construct()
    {
        //
    }

    /**
     * Returns number of errors
     * 
     * @return int
     */
    public function countErrors()
    {
        return count($this->errors);
    }

    /**
     * @return array Array of field wise errors
     */
    public function getInvalidFields()
    {
        return $this->errors;
    }

    /**
     * Set the data to-be validated
     */
    public function setData($data = [])
    {
        $this->rawData = $data;

        return $this;
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
                    $this->errors[] = $field;

                    // Skip other rules if one fails
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

        if (
            $rule !== 'required' && $rule !== 'accepted' && 
            empty($this->rawData[$field])
        ) {
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
        return !empty($this->rawData[$field]);
    }

    protected function validateAccepted($field)    
    {
        $acceptable = array('yes', 'on', 1, '1', true);
        return $this->validateRequired($field) && in_array($this->rawData[$field], $acceptable, true);
    }

    protected function validateEmail($field)
    {
        return filter_var($this->rawData[$field], FILTER_VALIDATE_EMAIL);
    }

    /**
     * Takes extra parameter if user wants to ignore spaces
     */
    protected function validateAlpha($field, $params = [0])
    {
        if ($params[0] === 1) {
            $subject = str_replace(' ', '', $this->rawData[$field]);
        } else {
            $subject = $this->rawData[$field];
        }
        return ctype_alpha($subject);
    }
    
    protected function validateAlphaNum($field) 
    {
        return ctype_alnum($this->rawData[$field]);
    }
    
    protected function validateDigit($field) 
    {
        return is_int($this->rawData[$field]) || ctype_digit($this->rawData[$field]);
    }
    
    protected function validateLength($field, $params)
    {
        $length = $params[0] ?? 0;
        $strlen = strlen($this->rawData[$field]);

        return $strlen === $length;
    }

    protected function validateLengthMax($field, $params)
    {
        $length = $params[0] ?? 0;
        $strlen = strlen($this->rawData[$field]);

        return $strlen <= $length;
    }

    protected function validateLengthBetween($field, $params) 
    {
        $min = $params[0] ?? 0;
        $max = $params[1] ?? 0;
        $strlen = strlen($this->rawData[$field]);

        return $strlen >= $min && $strlen <= $max;
    }
    
    protected function validateLengthMin($field, $params) 
    {
        $length = $params[0] ?? 0;
        $strlen = strlen($this->rawData[$field]);

        return $strlen >= $length;
    }

    protected function validateMin($field, $params)
    {
        $min = $params[0] ?? 0;
        return $this->rawData[$field] >= $min;
    }

    protected function validateMax($field, $params)
    {
        $max = $params[0] ?? 0;
        return $this->rawData[$field] <= $max;
    }

    protected function validateRegex($field, $params) 
    {
        $regex = $params[0] ?? '';
        return preg_match($regex, $this->rawData[$field]) === 1;
    }
}
