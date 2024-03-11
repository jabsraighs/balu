<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class PhoneTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (null === $value || '' === $value) {
            return '';
        }

        return '+33' . $value;
    }

    public function reverseTransform($value)
    {

        if (null === $value || '' === $value) {
            return null;
        }

        return substr($value, 3);
    }
}
