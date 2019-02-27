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
 * @author    Maxime LECLERCQ <maxime.leclercq@smile.fr>
 * @copyright 2019 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ScopedEav\Ui\Component\Attribute\Listing\Column;

use Smile\ScopedEav\Ui\Component\Model\Listing\Column\AbstractActions;

/**
 * Scoped EAV entity attribute grid actions column.
 *
 * @category Smile
 * @package  Smile\ScopedEav
 * @author   Aurelien FOUCRET <aurelien.foucret@smile.fr>
 */
class Actions extends AbstractActions
{
    /**
     * Return request field name.
     *
     * @return string
     */
    protected function getRequestFieldName(): string
    {
        return $this->getIndexField();
    }
}
