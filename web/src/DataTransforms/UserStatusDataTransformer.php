<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 02/11/2020
 * Time: 10:18
 */

namespace App\DataTransforms;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class UserStatusDataTransformer implements DataTransformerInterface
{
    public function transform($value): ?string
    {
        $status = $this->reverseTransform($value);

        return $status instanceof UserStatus ? $status->value() : null;
    }

    public function reverseTransform($value): ?UserStatus
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if ($value instanceof UserStatus) {
            return $value;
        }

        try {
            return UserStatus::byValue($value);
        } catch (\Throwable $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}