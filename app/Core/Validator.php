<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Rule strings such as "required|email|max:190". Returns cleaned values for the listed fields only,
 * so controllers never touch raw input that was not validated.
 */
final class Validator
{
    private array $errors = [];
    private array $clean = [];

    private function __construct(private array $data, private array $labels)
    {
    }

    /** @throws ValidationException */
    public static function validate(array $data, array $rules, array $labels = []): array
    {
        $v = new self($data, $labels);
        foreach ($rules as $field => $ruleString) {
            $v->check($field, is_array($ruleString) ? $ruleString : explode('|', $ruleString));
        }
        if ($v->errors) {
            throw new ValidationException($v->errors);
        }
        return $v->clean;
    }

    private function check(string $field, array $rules): void
    {
        $value = $this->data[$field] ?? null;
        if (is_string($value)) {
            $value = trim($value);
        }
        $empty = $value === null || $value === '' || $value === [];
        $isNumeric = in_array('numeric', $rules, true) || in_array('integer', $rules, true);

        if ($empty) {
            if (in_array('required', $rules, true)) {
                $this->fail($field, 'Please enter ' . $this->label($field) . '.');
            } elseif (in_array('boolean', $rules, true)) {
                $this->clean[$field] = false;
            } else {
                $this->clean[$field] = null;
            }
            return;
        }

        foreach ($rules as $rule) {
            [$name, $arg] = array_pad(explode(':', $rule, 2), 2, null);
            $message = $this->apply($name, $arg, $value, $field, $isNumeric);
            if ($message !== null) {
                $this->fail($field, $message);
                return;
            }
        }
        $this->clean[$field] = $this->cast($value, $rules);
    }

    private function apply(string $rule, ?string $arg, mixed $value, string $field, bool $isNumeric): ?string
    {
        $label = $this->label($field);
        $str = is_scalar($value) ? (string) $value : '';
        return match ($rule) {
            'required', 'nullable', 'string', 'boolean' => is_array($value) && $rule === 'string' ? ucfirst($label) . ' is not valid.' : null,
            'email'    => filter_var($str, FILTER_VALIDATE_EMAIL) && mb_strlen($str) <= 190 ? null : "That email address doesn't look right.",
            'numeric'  => is_numeric($str) ? null : ucfirst($label) . ' must be a number.',
            'integer'  => preg_match('/^-?\d+$/', $str) ? null : ucfirst($label) . ' must be a whole number.',
            'min'      => $isNumeric
                ? ((float) $str >= (float) $arg ? null : ucfirst($label) . " must be at least {$arg}.")
                : (mb_strlen($str) >= (int) $arg ? null : ucfirst($label) . " needs at least {$arg} characters."),
            'max'      => $isNumeric
                ? ((float) $str <= (float) $arg ? null : ucfirst($label) . " can be at most {$arg}.")
                : (mb_strlen($str) <= (int) $arg ? null : ucfirst($label) . " can be at most {$arg} characters."),
            'in'       => in_array($str, explode(',', (string) $arg), true) ? null : 'Please choose a valid ' . $label . '.',
            'confirmed'=> hash_equals($str, (string) ($this->data[$field . '_confirmation'] ?? '')) ? null : "The two passwords don't match.",
            'password' => mb_strlen($str) >= 8 && preg_match('/\pL/u', $str) && preg_match('/\d/', $str)
                ? null : 'Use at least 8 characters, including a letter and a number.',
            'date'     => $this->parseDate($str) ? null : ucfirst($label) . ' must be a valid date.',
            'slug'     => preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $str) ? null : 'Use lowercase letters, numbers and single hyphens only.',
            'phone'    => preg_match('/^\+?[0-9][0-9\s-]{6,17}$/', $str) ? null : 'Enter a valid phone number, for example 98XXXXXXXX.',
            'hex'      => preg_match('/^#[0-9a-fA-F]{6}$/', $str) ? null : ucfirst($label) . ' must be a colour like #A4471F.',
            'alpha_code'=> preg_match('/^[A-Z0-9-]{3,30}$/', strtoupper($str)) ? null : 'Use 3–30 letters, numbers or hyphens.',
            default    => throw new \LogicException("Unknown validation rule {$rule}"),
        };
    }

    private function cast(mixed $value, array $rules): mixed
    {
        if (in_array('boolean', $rules, true)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        if (in_array('integer', $rules, true)) {
            return (int) $value;
        }
        if (in_array('numeric', $rules, true)) {
            return round((float) $value, 2);
        }
        if (in_array('email', $rules, true)) {
            return mb_strtolower((string) $value);
        }
        if (in_array('date', $rules, true)) {
            return $this->parseDate((string) $value)?->format('Y-m-d H:i:s');
        }
        return $value;
    }

    private function parseDate(string $value): ?\DateTimeImmutable
    {
        foreach (['Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d'] as $format) {
            $d = \DateTimeImmutable::createFromFormat('!' . $format, $value);
            if ($d && $d->format($format) === $value) {
                return $d;
            }
        }
        return null;
    }

    private function label(string $field): string
    {
        return $this->labels[$field] ?? str_replace('_', ' ', $field);
    }

    private function fail(string $field, string $message): void
    {
        $this->errors[$field] ??= $message;
    }
}
