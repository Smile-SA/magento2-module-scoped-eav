<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Ui\Component\Entity\Listing\Column;

use Smile\ScopedEav\Ui\Component\Model\Listing\Column\AbstractActions;

/**
 * Scoped EAV entity grid actions column.
 */
class Actions extends AbstractActions
{
    /**
     * Return request field name.
     */
    protected function getRequestFieldName(): string
    {
        return 'id';
    }
}
