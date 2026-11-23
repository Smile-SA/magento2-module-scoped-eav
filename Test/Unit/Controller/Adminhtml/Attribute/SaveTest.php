<?php

declare(strict_types=1);

namespace Smile\ScopedEav\Test\Unit\Controller\Adminhtml\Attribute;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\FormData;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Smile\ScopedEav\Controller\Adminhtml\Attribute\BuilderInterface;
use Smile\ScopedEav\Controller\Adminhtml\Attribute\Save;
use Smile\ScopedEav\Model\Entity\Attribute;
use Smile\ScopedEav\ViewModel\Data as DataViewModel;

/**
 * The attribute save controller takes no structural field from the request.
 */
class SaveTest extends TestCase
{
    private const ENTITY_TYPE_ID = 12;

    private const DERIVED_SOURCE_MODEL = 'Derived\\SourceModel';

    private const DERIVED_BACKEND_MODEL = 'Derived\\BackendModel';

    private const STORED_ATTRIBUTE = [
        'attribute_id' => 150,
        'entity_type_id' => self::ENTITY_TYPE_ID,
        'attribute_code' => 'stored_code',
        'attribute_model' => null,
        'backend_model' => null,
        'backend_type' => 'varchar',
        'backend_table' => null,
        'frontend_model' => null,
        'frontend_input' => 'text',
        'frontend_label' => 'Stored label',
        'source_model' => null,
        'is_user_defined' => 0,
        'attribute_set_id' => null,
        'attribute_group_id' => null,
        'sort_order' => null,
    ];

    private const FORGED_VALUES = [
        'attribute_id' => 3,
        'entity_type_id' => 4,
        'attribute_code' => 'forged_code',
        'attribute_model' => 'Forged\\AttributeModel',
        'backend_model' => 'Forged\\BackendModel',
        'backend_type' => 'static',
        'backend_table' => 'admin_user',
        'frontend_model' => 'Forged\\FrontendModel',
        'source_model' => 'Forged\\SourceModel',
        'is_user_defined' => 1,
        'attribute_set_id' => 4,
        'attribute_group_id' => 7,
        'sort_order' => 1,
    ];

    private const NEW_ATTRIBUTE_EXPECTED = [
        'attribute_id' => null,
        'entity_type_id' => self::ENTITY_TYPE_ID,
        'attribute_model' => null,
        'backend_model' => self::DERIVED_BACKEND_MODEL,
        'backend_type' => 'varchar',
        'backend_table' => null,
        'frontend_model' => null,
        'source_model' => self::DERIVED_SOURCE_MODEL,
        'is_user_defined' => true,
        'attribute_set_id' => null,
        'attribute_group_id' => null,
        'sort_order' => null,
    ];

    /**
     * @var Http&Stub
     */
    private $request;

    /**
     * @var DataViewModel&Stub
     */
    private $dataViewModel;

    /**
     * @var BuilderInterface&Stub
     */
    private $builder;

    /**
     * @var Save
     */
    private $controller;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->request = $this->createStub(Http::class);
        $this->dataViewModel = $this->createStub(DataViewModel::class);
        $this->builder = $this->createStub(BuilderInterface::class);

        $redirect = $this->createStub(Redirect::class);
        $redirectFactory = $this->createStub(RedirectFactory::class);
        $redirectFactory->method('create')->willReturn($redirect);

        $context = $this->createStub(Context::class);
        $context->method('getRequest')->willReturn($this->request);
        $context->method('getResultRedirectFactory')->willReturn($redirectFactory);
        $context->method('getMessageManager')->willReturn($this->createStub(ManagerInterface::class));

        $this->controller = new Save(
            $context,
            $this->dataViewModel,
            $this->builder,
            new FormData(new Json()),
            $this->createStub(ForwardFactory::class)
        );
    }

    /**
     * Each structural field of an existing attribute keeps its stored value.
     *
     * @dataProvider existingAttributeFieldProvider
     */
    #[DataProvider('existingAttributeFieldProvider')]
    public function testExistingAttributeKeepsStoredField(string $field): void
    {
        $attribute = $this->createAttribute(self::STORED_ATTRIBUTE);
        $this->givenPost([$field => self::FORGED_VALUES[$field], 'frontend_label' => ['New label']]);

        $this->controller->execute();

        $this->assertSame(self::STORED_ATTRIBUTE[$field], $attribute->getData($field));
        $this->assertSame(['New label'], $attribute->getData('frontend_label'));
    }

    /**
     * The input type of an existing attribute keeps its stored value.
     */
    public function testExistingAttributeKeepsStoredFrontendInput(): void
    {
        $attribute = $this->createAttribute(self::STORED_ATTRIBUTE);
        $this->givenPost(['frontend_input' => 'select', 'frontend_label' => ['New label']]);

        $this->controller->execute();

        $this->assertSame('text', $attribute->getData('frontend_input'));
        $this->assertSame(['New label'], $attribute->getData('frontend_label'));
    }

    /**
     * A new attribute takes no structural field from the request.
     *
     * @dataProvider newAttributeFieldProvider
     */
    #[DataProvider('newAttributeFieldProvider')]
    public function testNewAttributeIgnoresForgedField(string $field): void
    {
        $attribute = $this->createAttribute(['entity_type_id' => self::ENTITY_TYPE_ID]);
        $this->dataViewModel->method('getAttributeSourceModelByInputType')->willReturn(self::DERIVED_SOURCE_MODEL);
        $this->dataViewModel->method('getAttributeBackendModelByInputType')->willReturn(self::DERIVED_BACKEND_MODEL);
        $this->givenPost([
            'attribute_code' => 'new_code',
            'frontend_input' => 'text',
            'frontend_label' => ['New label'],
            $field => self::FORGED_VALUES[$field],
        ]);

        $this->controller->execute();

        $this->assertSame(self::NEW_ATTRIBUTE_EXPECTED[$field], $attribute->getData($field));
        $this->assertSame('new_code', $attribute->getData('attribute_code'));
        $this->assertSame(['New label'], $attribute->getData('frontend_label'));
        $this->assertSame(self::DERIVED_SOURCE_MODEL, $attribute->getData('source_model'));
        $this->assertSame(self::DERIVED_BACKEND_MODEL, $attribute->getData('backend_model'));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function existingAttributeFieldProvider(): array
    {
        return self::fieldCases(array_keys(self::FORGED_VALUES));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function newAttributeFieldProvider(): array
    {
        return self::fieldCases(array_keys(self::NEW_ATTRIBUTE_EXPECTED));
    }

    /**
     * @param string[] $fields
     * @return array<string, array{string}>
     */
    private static function fieldCases(array $fields): array
    {
        $cases = [];
        foreach ($fields as $field) {
            $cases[$field] = [$field];
        }

        return $cases;
    }

    /**
     * @param array<string, mixed> $post
     */
    private function givenPost(array $post): void
    {
        $this->request->method('getPostValue')->willReturn($post);
        $this->request->method('getParam')->willReturnCallback(
            static function (string $key, $default = null) use ($post) {
                if ($key === 'serialized_options') {
                    return '[]';
                }

                return $post[$key] ?? $default;
            }
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return Attribute&MockObject
     */
    private function createAttribute(array $data): MockObject
    {
        $attribute = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'getDefaultValueByInput', 'getBackendTypeByInput'])
            ->getMock();
        $attribute->setIdFieldName('attribute_id');
        $attribute->setData($data);
        $attribute->method('getDefaultValueByInput')->willReturn('');
        $attribute->method('getBackendTypeByInput')->willReturn('varchar');
        $attribute->expects($this->once())->method('save')->willReturnSelf();
        $this->builder->method('build')->willReturn($attribute);

        return $attribute;
    }
}
