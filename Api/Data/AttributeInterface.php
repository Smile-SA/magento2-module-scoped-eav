<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Api\Data;

/**
 * Scoped attribute interface.
 *
 * @api
 */
interface AttributeInterface extends \Magento\Eav\Api\Data\AttributeInterface
{
    public const SCOPE_STORE_TEXT = 'store';

    public const SCOPE_GLOBAL_TEXT = 'global';

    public const SCOPE_WEBSITE_TEXT = 'website';

    public const SCOPE_STORE = 0;

    public const SCOPE_GLOBAL = 1;

    public const SCOPE_WEBSITE = 2;

    public const KEY_IS_GLOBAL = 'is_global';

    /**
     * Retrieve attribute scope as text.
     *
     * @return string|null
     */
    public function getScope(): ?string;

    /**
     * Return is attribute global as int.
     *
     * @return int|string
     */
    public function getIsGlobal();

    /**
     * Set attribute scope
     *
     * @param string $scope Attribute scope as text.
     * @return $this
     */
    public function setScope(string $scope): self;

    /**
     * Set is_global value.
     *
     * @param int $isGlobal Attribute scope as int.
     * @return $this
     */
    public function setIsGlobal(int $isGlobal): self;

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal(): bool;

    /**
     * Retrieve attribute is website scope website
     *
     * @return bool
     */
    public function isScopeWebsite(): bool;

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore(): bool;
}
