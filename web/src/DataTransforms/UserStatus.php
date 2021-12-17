<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 02/11/2020
 * Time: 10:14
 */

namespace App\DataTransforms;


final class UserStatus
{
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_PENDING_FOR_APPROVAL = 'PENDING_FOR_APPROVAL';

    private static $instances = [];

    private  $value;

    private function __construct(string $value)
    {
        if (!array_key_exists($value, self::choices())) {
            throw new \DomainException(sprintf('The value "%s" is not a valid moderation status.', $value));
        }

        $this->value = $value;
    }

    public static function byValue(string $value): UserStatus
    {
        // limitation of count object instances
        if (!isset(self::$instances[$value])) {
            self::$instances[$value] = new static($value);
        }

        return self::$instances[$value];
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function choices(): array
    {
        return [
            self::STATUS_PENDING_FOR_APPROVAL => 'Pendiente de aprobación',
            self::STATUS_APPROVED => 'Aprobado',
            self::STATUS_REJECTED => 'Rechazado',
            self::STATUS_CLOSED => 'Cerrado',
        ];
    }

    public function __toString(): string
    {
        return self::choices()[$this->value];
    }
}