<?php namespace Lkt\Generated;

use Lkt\Factory\Instantiator\Instantiator;
use Lkt\QueryBuilding\Query;

class GeneratedLktWebElement extends \Lkt\Factory\Instantiator\Instances\AbstractInstance
{
    const COMPONENT = 'lkt-web-element';

    public function getId(): int
    {
        return $this->_getIntegerVal('id');
    }

    public function hasId(): bool
    {
        return $this->_hasIntegerVal('id');
    }

    /** @return \Lkt\Instances\LktWebElement */
    public function setId(int $id)
    {
        $this->_setIntegerVal('id', $id);
        return $this;
    }

    public function getCreatedAt(): ?\Carbon\Carbon
    {
        return $this->_getDateTimeVal('createdAt');
    }

    public function getCreatedAtFormatted(string $format = null): string
    {
        return $this->_getDateTimeFormattedVal('createdAt', $format);
    }

    public function getCreatedAtIntlFormatted(string $format = null): string
    {
        return $this->_getDateTimeFormattedIntlVal('createdAt', $format);
    }

    public function hasCreatedAt(): bool
    {
        return $this->_hasDateTimeVal('createdAt');
    }

    /** @return \Lkt\Instances\LktWebElement */
    public function setCreatedAt($createdAt)
    {
        $this->_setDateTimeVal('createdAt', $createdAt);
        return $this;
    }

    public function getUpdatedAt(): ?\Carbon\Carbon
    {
        return $this->_getDateTimeVal('updatedAt');
    }

    public function getUpdatedAtFormatted(string $format = null): string
    {
        return $this->_getDateTimeFormattedVal('updatedAt', $format);
    }

    public function getUpdatedAtIntlFormatted(string $format = null): string
    {
        return $this->_getDateTimeFormattedIntlVal('updatedAt', $format);
    }

    public function hasUpdatedAt(): bool
    {
        return $this->_hasDateTimeVal('updatedAt');
    }

    /** @return \Lkt\Instances\LktWebElement */
    public function setUpdatedAt($updatedAt)
    {
        $this->_setDateTimeVal('updatedAt', $updatedAt);
        return $this;
    }

    public function getType(): int
    {
        return $this->_getIntegerVal('type');
    }

    public function hasType(): bool
    {
        return $this->_hasIntegerVal('type');
    }

    /** @return \Lkt\Instances\LktWebElement */
    public function setType(int $type)
    {
        $this->_setIntegerVal('type', $type);
        return $this;
    }

    public function getComponent(): string
    {
        return $this->_getStringVal('component');
    }

    public function hasComponent(): bool
    {
        return $this->_hasStringVal('component');
    }

    /** @return \Lkt\Instances\LktWebElement */
    public function setComponent(string $component)
    {
        $this->_setStringVal('component', $component);
        return $this;
    }

    public function getProps(): ?array
    {
        return $this->_getJsonVal('props');
    }

    /** @return \Lkt\Instances\LktWebElement */
    public function setProps(array $props)
    {
        $this->_setJsonVal('props', $props);
        return $this;
    }

    public function hasProps(): bool
    {
        return $this->_hasJsonVal('props');
    }

    public function getConfig(): ?array
    {
        return $this->_getJsonVal('config');
    }

    /** @return \Lkt\Instances\LktWebElement */
    public function setConfig(array $config)
    {
        $this->_setJsonVal('config', $config);
        return $this;
    }

    public function hasConfig(): bool
    {
        return $this->_hasJsonVal('config');
    }

    public function getLayout(): ?array
    {
        return $this->_getJsonVal('layout');
    }

    /** @return \Lkt\Instances\LktWebElement */
    public function setLayout(array $layout)
    {
        $this->_setJsonVal('layout', $layout);
        return $this;
    }

    public function hasLayout(): bool
    {
        return $this->_hasJsonVal('layout');
    }

    public function getSubElements(): ?array
    {
        return $this->_getJsonVal('subElements');
    }

    /** @return \Lkt\Instances\LktWebElement */
    public function setSubElements(array $subElements)
    {
        $this->_setJsonVal('subElements', $subElements);
        return $this;
    }

    public function hasSubElements(): bool
    {
        return $this->_hasJsonVal('subElements');
    }

    public function getChildren(): string
    {
        return $this->_getForeignListVal('children');
    }

    public function hasChildren(): bool
    {
        return $this->_hasForeignListVal('children');
    }

    /** @return \Lkt\Instances\LktWebElement */
    public function setChildren($children)
    {
        $this->_setForeignListVal('children', $children);
        return $this;
    }

    /** @return \Lkt\Instances\LktWebElement */
    public function removeChildrenIds(array $ids)
    {
        $this->_removeForeignListIds('children', $ids);
        return $this;
    }

    /** @return \Lkt\Instances\LktWebElement[] */
    public function getChildrenData(): array
    {
        return $this->_getForeignListData('children');
    }

    public function getChildrenIds(): array
    {
        return $this->_getForeignListIds('children');
    }

    /** * @return \Lkt\Instances\LktWebElement[] */
    public static function getMany(Query $builder = null): array
    {
        /** @var \Lkt\Instances\LktWebElement[] */
        $r = parent::getMany($builder);
        return $r;
    }

    /** * @return \Lkt\Instances\LktWebElement|null */
    public static function getOne(Query $builder = null)
    {
        /** @var \Lkt\Instances\LktWebElement */
        $r = parent::getOne($builder);
        return $r;
    }

    /** @return \Lkt\Generated\LktWebElementQueryBuilder */
    public static function getQueryCaller()
    {
        /** * @var \Lkt\Generated\LktWebElementQueryBuilder $builder */
        list($builder) = Instantiator::getCustomQueryCaller(static::COMPONENT);
        return $builder;
    }

    /** @return \Lkt\Generated\LktWebElementQueryBuilder */
    public static function getFilteredQueryCaller(array $data, array $processRules = null, array $filterRules = null)
    {
        /** * @var \Lkt\Generated\LktWebElementQueryBuilder $caller */
        list($builder) = Instantiator::getCustomQueryCaller(static::COMPONENT, $data, $processRules, $filterRules);
        return $builder;
    }

    /** * @return \Lkt\Instances\LktWebElement[] */
    public static function getPage(int $page, Query $builder = null, int $itemsPerPage = 0): array
    {
        /** @var \Lkt\Instances\LktWebElement[] */
        $r = parent::getPage($page, $builder, $itemsPerPage);
        return $r;
    }
}