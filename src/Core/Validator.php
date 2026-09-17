<?php

namespace App\Core;

/**
 * Form and Request Input Validator
 */
class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($ruleList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                match ($rule) {
                    'required' => $this->validateRequired($field, $value),
                    'email'    => $this->validateEmail($field, $value),
                    'min'      => $this->validateMin($field, $value, (int) ($params[0] ?? 0)),
                    'max'      => $this->validateMax($field, $value, (int) ($params[0] ?? 0)),
                    'in'       => $this->validateIn($field, $value, $params),
                    'integer'  => $this->validateInteger($field, $value),
                    default    => null,
                };
            }
        }

        return empty($this->errors);
    }

    private function validateRequired(string $field, mixed $value): void
    {
        if ($value === null || (is_string($value) && trim($value) === '')) {
            $this->addError($field, "กรุณากรอก {$field}");
        }
    }

    private function validateEmail(string $field, mixed $value): void
    {
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "รูปแบบอีเมลไม่ถูกต้อง");
        }
    }

    private function validateMin(string $field, mixed $value, int $min): void
    {
        if (is_string($value) && mb_strlen($value) < $min) {
            $this->addError($field, "{$field} ต้องมีความยาวอย่างน้อย {$min} ตัวอักษร");
        }
    }

    private function validateMax(string $field, mixed $value, int $max): void
    {
        if (is_string($value) && mb_strlen($value) > $max) {
            $this->addError($field, "{$field} ต้องมีความยาวไม่เกิน {$max} ตัวอักษร");
        }
    }

    private function validateIn(string $field, mixed $value, array $allowed): void
    {
        if (!in_array((string)$value, $allowed, true)) {
            $this->addError($field, "ค่าของ {$field} ไม่ถูกต้อง");
        }
    }

    private function validateInteger(string $field, mixed $value): void
    {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError($field, "{$field} ต้องเป็นตัวเลขจำนวนเต็ม");
        }
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }
        return null;
    }
}
