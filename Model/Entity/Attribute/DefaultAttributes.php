<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ScopedEav
 * @author    Aurelien FOUCRET <aurelien.foucret@smile.fr>
 * @copyright 2017 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Model\Entity\Attribute;

use Magento\CustomerSegment\Observer\EnterpiseCustomerAttributeEditPrepareFormObserver;
use Magento\Eav\Model\ResourceModel\Attribute\DefaultEntityAttributes\ProviderInterface;
use Smile\ScopedEav\Api\Data\EntityInterface;

/**
 * Scoped EAV entity default attributes..
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class DefaultAttributes implements ProviderInterface
{
    /**
     * List of default entity attributes.
     *
     * @return array
     */
    public function getDefaultAttributes()
    {
        return [
            EntityInterface::IS_ACTIVE,
            EntityInterface::NAME,
        ];
    }
}
