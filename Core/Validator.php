<?php

declare(strict_types=1);

namespace Core;

use Core\Database\Semiloquent;

/**
 * TODO: make validateMatch more flexible
 * TODO: Solve Semiloquent problem in validateUnique
 */

class Validator
{
    private const RULE_REQUIRED = 'required';
    private const RULE_EMAIL = 'email';
    private const RULE_MIN = 'min';
    private const RULE_MAX = 'max';
    private const RULE_MATCH = 'match';
    private const RULE_UNIQUE = 'unique';
    private const RULE_STRING = 'string';
    private const RULE_INTEGER = 'integer';

    private array $errors = [];
    private array $rules = [];
    private array $data = [];

    private string $match;

    public array $errorMessages = [
        self::RULE_REQUIRED => 'This field is required',
        self::RULE_EMAIL => 'This field must be valid email address',
        self::RULE_MIN => 'Min length of this field must be {min}',
        self::RULE_MAX => 'Max length of this field must be {max}',
        self::RULE_MATCH => 'This field must be the same as {match}',
        self::RULE_UNIQUE => 'Record with with this {unique} already exists',
        self::RULE_STRING => "This field must be string",
        self::RULE_INTEGER => "This field must be integer",
    ];

    public function validate(array $rules, array $data): void
    {
        $this->rules = $rules;
        $this->data  = $data;
        $parsedRules = $this->parser($this->rules);

        foreach ($parsedRules as $attribute => $validations) {
            foreach ($validations as $rule => $parameter) {
                $value = $data[$attribute];

                if (! is_int($rule)) {
                    $validationFunction = "validate" . ucfirst($rule);

                    if (method_exists($this, $validationFunction)) {
                        $this->$validationFunction($attribute, $value, $parameter);
                    }
                } else {
                    $validationFunction = "validate" . ucfirst($parameter);

                    if (method_exists($this, $validationFunction)) {
                        $this->$validationFunction($attribute, $value);
                    }
                }
            }
        }
    }

    protected function parser(array $rules): array
    {
        $parsedRules = [];

        foreach ($rules as $rule => $key) {
            $keyValues = explode('|', $key);
            $parsedRules[$rule] = [];

            foreach ($keyValues as $value) {
                if (strpos($value, ':')) {
                    $explodedValue = explode(':', $value);
                    $parsedRules[$rule][$explodedValue[0]] = $explodedValue[1];
                } else {
                    array_push($parsedRules[$rule], $value);
                }
            }
        }

        return $parsedRules;
    }

    protected function validateRequired(string $attribute, string $value): void
    {
        if (empty($value)) {
            $this->addErrorByRule($attribute, self::RULE_REQUIRED);
        }

        // This stupid condition should be removed
        if ($attribute === 'password') {
            $this->match = $value;
        }
    }

    protected function validateString(string $attribute, string $value): void
    {
        if (! is_string($value) || empty($value)) {
            $this->addErrorByRule($attribute, self::RULE_STRING);
        }
    }
    
    protected function validateEmail(string $attribute, string $value): void
    {
        $pattern = "/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/";

        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            if (! preg_match($pattern, $value)) {
                $this->addErrorByRule($attribute, self::RULE_EMAIL);
            }
        }
    }
    
    protected function validateMin(string $attribute, string $value, string $parameter): void
    {
        if (strlen($value) < (int) $parameter) {
            $this->addErrorByRule($attribute, self::RULE_MIN, ['min' => $parameter]);
        }
    }
    
    protected function validateMax(string $attribute, string $value, string $parameter): void
    {
        if (strlen($value) > (int) $parameter) {
            $this->addErrorByRule($attribute, self::RULE_MIN, ['max' => $parameter]);
        }
    }
    
    protected function validateMatch(string $attribute, string $value, string $parameter): void
    {
        if ($value !== $this->match) {
            $this->addErrorByRule($attribute, self::RULE_MATCH, ['match' => $parameter]);
        }
    }
    
    protected function validateUnique(string $attribute, string $value, string $parameter): void
    {
        // $record = (new Semiloquent($parameter))->find($value);

        $record = true;

        if (! $record) {
            $this->addErrorByRule($attribute, self::RULE_UNIQUE, ['unique' => $value]);
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        if (empty($this->getErrors())) {
            return $this->data;
        }

        return $this->getErrors();
    }
    
    protected function addErrorByRule(string $attribute, string $value, array $params = []): void
    {
        $errorMessage = $this->errorMessages[$value];

        foreach ($params as $key => $value) {
            $errorMessage = str_replace("{{$key}}", $value, $errorMessage);
        } 

        $this->errors[$attribute][] = $errorMessage;
    }

    public function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute][] = $message;
    }

    public function hasError(string $attribute): string|bool
    {
        return $this->errors[$attribute] ?? false;
    }
}

$validator = new Validator();

// $validator->validate(
//     [
//         'name' => 'required|string|unique:users',
//         'email' => 'required|email',
//         'password' => 'required|max:16|min:8',
//         'passwordConfirm' => 'required|match:password'
//     ],
//     [
//         'name' => 'Ali',
//         'email' => 'alisalim@gmail.com',
//         'password' => '12345678',
//         'passwordConfirm' => '12345678'
//     ]
// );

// print_r($validator->validated());