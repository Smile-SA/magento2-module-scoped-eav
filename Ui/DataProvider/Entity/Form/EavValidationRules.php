<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\DataProvider\Entity\Form;

use Smile\ScopedEav\Api\Data\AttributeInterface;

/**
 * Scoped EAV form validation rules.
 */
class EavValidationRules
{
    /**
     * Build validation rules for the attribute.
     *
     * @param AttributeInterface $attribute Attribute.
     * @param array $data Additional data.
     * @return array
     */
    public function build(AttributeInterface $attribute, array $data): array
    {
        $rules = [];

        if (!empty($data['required'])) {
            $rules['required-entry'] = true;
        }

        $validationClasses = explode(' ', (string) $attribute->getFrontendClass());

        foreach ($validationClasses as $class) {
            if (preg_match('/^maximum-length-(\d+)$/', $class, $matches)) {
                // @codingStandardsIgnoreLine
                $rules = array_merge($rules, ['max_text_length' => $matches[1]]);
                continue;
            }
            if (preg_match('/^minimum-length-(\d+)$/', $class, $matches)) {
                // @codingStandardsIgnoreLine
                $rules = array_merge($rules, ['min_text_length' => $matches[1]]);
                continue;
            }

            $rules = $this->mapRules($class, $rules);
        }

        return $rules;
    }

    /**
     * Map fontend classes with rules.
     *
     * @param string $class Frontend class.
     * @param array  $rules Validation rules.
     * @return array
     */
    private function mapRules(string $class, array $rules): array
    {
        switch ($class) {
            case 'validate-number':
            case 'validate-digits':
            case 'validate-email':
            case 'validate-url':
            case 'validate-alpha':
            case 'validate-alphanum':
                $rules = array_merge($rules, [$class => true]);
                break;
        }

        return $rules;
    }
}
