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

namespace Smile\ScopedEav\Controller\Adminhtml\Entity;

/**
 * Scoped EAV entity builder interface.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
interface BuilderInterface
{
    /**
     * Retrieve and init an entity from the request.
     *
     * @param \Magento\Framework\App\RequestInterface $request Request.
     *
     * @return \Smile\ScopedEav\Api\Data\EntityInterface
     */
    public function build(\Magento\Framework\App\RequestInterface $request);
}
