<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Controller\Adminhtml\Attribute;

/**
 * Scoped EAV entity attribute edit controller.
 */
class Edit extends \Smile\ScopedEav\Controller\Adminhtml\AbstractAttribute
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $title     = __('New attribute');
            $attribute = $this->getAttribute();
            if ($attribute->getAttributeId()) {
                $title = __('Edit %1', $attribute->getAttributeCode());
            }
            $response = $this->createActionPage($title);
        } catch (\Exception $e) {
            $response = $this->getRedirectError($e->getMessage());
        }

        return $response;
    }
}
