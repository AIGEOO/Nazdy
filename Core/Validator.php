<?php

declare(strict_types=1);

namespace Core;

use Core\Database\Semiloquent;

class Validator
{
    const ERROR_DEFAULT = 'Invalid';
    protected array $_fields = [];
    protected array $_errors = [];
    protected array $_validations = [];
    protected array $_labels = [];
    protected static array $_rules = [];
    protected bool $prepend_labels = true;
    protected bool $stop_on_first_fail = false;

    protected static array $_ruleMessages = [
        'match' => 'should be match with the above',
        'numeric' => 'must be numeric',
        'integer' => 'must be integer',
        'lengthMin' => 'min length must be 8',
        'lengthMax' => 'max length must be 16',
        'email' => 'must be a valid email address',
        'alphaNum' => 'must be contain either alphabetical or intger characters',
        'slug' => 'must be a valid slug e.g. (this-is-example)',
        'password' => 'must be a valid password contain: 
            - 2 uppercase letters
            - 1 special case letter
            - 2 digits
            - 3 lowercase letters
            - length in range 8 - 16
        ',
        'unique' => 'record is already exists',
        'creditCard' => 'must be a valid credit card number',
        'boolean' => 'must be boolean',
    ];

    public function __construct(array $data = [], array $fields = [])
    {
        $this->_fields = !empty($fields) ? array_intersect_key($data, array_flip($fields)) : $data;
    }

    protected function validateRequired(string $field, string $value, array $params = []): bool
    {
        if (isset($params[0]) && (bool)$params[0]) {
            $find = $this->getPart($this->_fields, explode('.', $field), true);
            return $find[1];
        }

        if (is_null($value) || (is_string($value) && trim($value) === '')) {
            return false;
        }

        return true;
    }

    protected function validateMatch(string $field, string $value, array $params): bool
    {
        list($field2Value, $multiple) = $this->getPart($this->_fields, explode('.', $params[0]));
        return isset($field2Value) && $value == $field2Value;
    }

    protected function validateNumeric(string $field, string $value): bool
    {
        return is_numeric($value);
    }

    protected function validateInteger(string $field, string $value, array $params): bool
    {
        if (isset($params[0]) && (bool)$params[0]) {
            return preg_match('/^([0-9]|-[1-9]|-?[1-9][0-9]*)$/i', $value);
        }

        return filter_var($value, \FILTER_VALIDATE_INT) !== false;
    }

    protected function validateLengthMin(string $field, string $value, array $params): bool
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length >= $params[0];
    }

    protected function validateLengthMax(string $field, string $value, array $params): bool
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length <= $params[0];
    }

    protected function stringLength($value): int
    {
        if (! is_string($value)) {
            return false;
        } elseif (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return strlen($value);
    }

    protected function validateSizeMin(string $field, string $value, array $params): bool
    {
        if (!is_numeric($value)) {
            return false;
        } elseif (function_exists('bccomp')) {
            return !(bccomp($params[0], $value, 14) === 1);
        } else {
            return $params[0] <= $value;
        }
    }

    protected function validateSizeMax(string $field, string $value, array $params): bool
    {
        if (!is_numeric($value)) {
            return false;
        } elseif (function_exists('bccomp')) {
            return !(bccomp($value, $params[0], 14) === 1);
        } else {
            return $params[0] >= $value;
        }
    }

    protected function validateEmail(string $field, string $value): bool
    {
        return filter_var($value, \FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateAlphaNum(string $field, string $value): bool
    {
        return preg_match('/^([a-z0-9])+$/i', $value);
    }

    protected function validateSlug(string $field, string $value): bool
    {
        if (is_array($value)) {
            return false;
        }
        return preg_match('/^([-a-z0-9_-])+$/i', $value);
    }

    protected function validateRegex(string $field, string $value, array $params): bool
    {
        return preg_match($params[0], $value);
    }

    protected function validateDateFormat(string $field, string $value, array $params): bool
    {
        $parsed = date_parse_from_format($params[0], $value);

        return $parsed['error_count'] === 0 && $parsed['warning_count'] === 0;
    }

    protected function validateBoolean(string $field, string $value): bool
    {
        return is_bool($value);
    }

    protected function validateCreditCard(string $field, string $value, array $params): bool
    {
        if (!empty($params)) {
            if (is_array($params[0])) {
                $cards = $params[0];
            } elseif (is_string($params[0])) {
                $cardType = $params[0];
                if (isset($params[1]) && is_array($params[1])) {
                    $cards = $params[1];
                    if (!in_array($cardType, $cards)) {
                        return false;
                    }
                }
            }
        }
        /**
         * Luhn algorithm
         */
        $numberIsValid = function () use ($value) {
            $number = preg_replace('/[^0-9]+/', '', $value);
            $sum = 0;

            $strlen = strlen($number);
            if ($strlen < 13) {
                return false;
            }
            for ($i = 0; $i < $strlen; $i++) {
                $digit = (int)substr($number, $strlen - $i - 1, 1);
                if ($i % 2 == 1) {
                    $sub_total = $digit * 2;
                    if ($sub_total > 9) {
                        $sub_total = ($sub_total - 10) + 1;
                    }
                } else {
                    $sub_total = $digit;
                }
                $sum += $sub_total;
            }
            if ($sum > 0 && $sum % 10 == 0) {
                return true;
            }

            return false;
        };

        if ($numberIsValid()) {
            if (!isset($cards)) {
                return true;
            } else {
                $cardRegex = array(
                    'visa' => '#^4[0-9]{12}(?:[0-9]{3})?$#',
                    'mastercard' => '#^(5[1-5]|2[2-7])[0-9]{14}$#',
                    'amex' => '#^3[47][0-9]{13}$#',
                    'dinersclub' => '#^3(?:0[0-5]|[68][0-9])[0-9]{11}$#',
                    'discover' => '#^6(?:011|5[0-9]{2})[0-9]{12}$#',
                );

                if (isset($cardType)) {
                    if (!isset($cards) && !in_array($cardType, array_keys($cardRegex))) {
                        return false;
                    }

                    return (preg_match($cardRegex[$cardType], $value) === 1);

                } elseif (isset($cards)) {
                    foreach ($cards as $card) {
                        if (in_array($card, array_keys($cardRegex)) && preg_match($cardRegex[$card], $value) === 1) {
                            return true;
                        }
                    }
                } else {
                    foreach ($cardRegex as $regex) {
                        if (preg_match($regex, $value) === 1) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * check if a password meets a specific strength criteria
     * 
     * (?=.*[A-Z].*[A-Z])        Ensure string has 2 uppercase letters.
     * (?=.*[!@#$&*])            Ensure string has 1 special case letter.
     * (?=.*[0-9].*[0-9])        Ensure string has 2 digits.
     * (?=.*[a-z].*[a-z].*[a-z]) Ensure string has 3 lowercase letters.
     * .{8,16}                   Ensure string is of length in range 8 - 16. 
     */
    protected function validatePassword(string $field, string $value): bool 
    {
        $regex = '/^(?=.*[A-Z].*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8,16}$/';
        return preg_match($regex, $value) ? true : false;
    }

    protected function validateUnique(string $field, string $value, array $params) 
    {
        return empty((new Semiloquent($params[0]))->where($field, '=', $value)->get());
    }

    public function data(): array
    {
        return $this->_fields;
    }

    public function errors(?string $field = null): array
    {
        if ($field !== null) {
            return isset($this->_errors[$field]) ? $this->_errors[$field] : false;
        }

        return $this->_errors;
    }

    public function addError(string $field, string $message, array $params = []): void
    {
        $message = $this->checkAndSetLabel($field, $message, $params);

        $values = [];
        foreach ($params as $param) {
            if (is_array($param)) {
                $param = "['" . implode("', '", $param) . "']";
            }
            if ($param instanceof \DateTime) {
                $param = $param->format('Y-m-d');
            } else {
                if (is_object($param)) {
                    $param = get_class($param);
                }
            }
            if (is_string($params[0]) && isset($this->_labels[$param])) {
                $param = $this->_labels[$param];
            }
            $values[] = $param;
        }

        $this->_errors[$field][] = vsprintf($message, $values);
    }

    public function message(string $message): self
    {
        $this->_validations[count($this->_validations) - 1]['message'] = $message;

        return $this;
    }

    protected function getPart(array $data, array $identifiers, bool $allow_empty = false): array
    {
        if (is_array($identifiers) && count($identifiers) === 0) {
            return array($data, false);
        }
        if (is_scalar($data)) {
            return array(null, false);
        }
        $identifier = array_shift($identifiers);
        if ($identifier === '*') {
            $values = [];
            foreach ($data as $row) {
                list($value, $multiple) = $this->getPart($row, $identifiers, $allow_empty);
                if ($multiple) {
                    $values = array_merge($values, $value);
                } else {
                    $values[] = $value;
                }
            }
            return array($values, true);
        } 
        elseif ($identifier === null || ! isset($data[$identifier])) {
            if ($allow_empty){
                return array(null, array_key_exists($identifier, $data));
            }
            return array(null, false);
        } 
        elseif (count($identifiers) === 0) {
            if ($allow_empty) {
                return array(null, array_key_exists($identifier, $data));
            }
            return array($data[$identifier], $allow_empty);
        } 
        else {
            return $this->getPart($data[$identifier], $identifiers, $allow_empty);
        }
    }

    public function validate(): bool
    {
        $set_to_break = false;
        foreach ($this->_validations as $v) {
            foreach ($v['fields'] as $field) {
                list($values, $multiple) = $this->getPart($this->_fields, explode('.', $field), false);

                $errors = $this->getRules();
                if (isset($errors[$v['rule']])) {
                    $callback = $errors[$v['rule']];
                } else {
                    $callback = array($this, 'validate' . ucfirst($v['rule']));
                }

                if (!  $multiple) {
                    $values = array($values);
                } else if (! $this->hasRule('required', $field)){
                    $values = array_filter($values);
                }

                $result = true;
                foreach ($values as $value) {
                    $result = $result && call_user_func($callback, $field, $value, $v['params'], $this->_fields);
                }

                if (! $result) {
                    $this->addError($field, $v['message'], $v['params']);
                    if ($this->stop_on_first_fail) {
                        $set_to_break = true;
                        break;
                    }
                }
            }
            if ($set_to_break) {
                break;
            }
        }

        return count($this->errors()) === 0;
    }

    protected function getRules(): array
    {
        return static::$_rules;
    }

    protected function getRuleMessages(): array
    {
        return static::$_ruleMessages;
    }

    protected function hasRule(string $name, string $field): bool
    {
        foreach ($this->_validations as $validation) {
            if ($validation['rule'] == $name && in_array($field, $validation['fields'])) {
                return true;
            }
        }

        return false;
    }

    public function rule(string $rule, string $fields): self
    {
        $params = array_slice(func_get_args(), 2);

        $errors = $this->getRules();
        if (!isset($errors[$rule])) {
            $ruleMethod = 'validate' . ucfirst($rule);
            if (!method_exists($this, $ruleMethod)) {
                throw new \InvalidArgumentException(
                    "Rule '" . $rule . "' has not been registered with " . get_called_class() . "::addRule()."
                );
            }
        }

        $messages = $this->getRuleMessages();
        $message = isset($messages[$rule]) ? $messages[$rule] : self::ERROR_DEFAULT;

        if (function_exists('mb_strpos')) {
            $notContains = mb_strpos($message, '{field}') === false;
        } else {
            $notContains = strpos($message, '{field}') === false;
        }
        if ($notContains) {
            $message = '{field} ' . $message;
        }

        $this->_validations[] = array(
            'rule' => $rule,
            'fields' => (array)$fields,
            'params' => (array)$params,
            'message' => $message
        );

        return $this;
    }

    public function label(string $value): self
    {
        $lastRules = $this->_validations[count($this->_validations) - 1]['fields'];
        $this->labels(array($lastRules[0] => $value));

        return $this;
    }

    public function labels(array $labels = []): self
    {
        $this->_labels = array_merge($this->_labels, $labels);

        return $this;
    }

    protected function checkAndSetLabel(string $field, string $message, array $params): string
    {
        if (isset($this->_labels[$field])) {
            $message = str_replace('{field}', $this->_labels[$field], $message);

            if (is_array($params)) {
                $i = 1;
                foreach ($params as $k => $v) {
                    $tag = '{field' . $i . '}';
                    $label = isset($params[$k]) && (is_numeric($params[$k]) || is_string($params[$k])) && isset($this->_labels[$params[$k]]) ? $this->_labels[$params[$k]] : $tag;
                    $message = str_replace($tag, $label, $message);
                    $i++;
                }
            }
        } else {
            $message = $this->prepend_labels
                ? str_replace('{field}', ucwords(str_replace('_', ' ', $field)), $message)
                : str_replace('{field} ', '', $message);
        }

        return $message;
    }

    public function rules(array $rules): void
    {
        foreach ($rules as $ruleType => $params) {
            if (is_array($params)) {
                foreach ($params as $innerParams) {
                    if (!is_array($innerParams)) {
                        $innerParams = (array)$innerParams;
                    }
                    array_unshift($innerParams, $ruleType);
                    call_user_func_array(array($this, 'rule'), $innerParams);
                }
            } else {
                $this->rule($ruleType, $params);
            }
        }
    }
}
