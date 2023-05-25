<?php

declare(strict_types=1);

namespace Rector\Ray\AnnotationBinding\Rector\ClassMethod;

use Doctrine\Common\Annotations\AnnotationReader;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Ray\Di\Di\Named;
use Ray\Di\Di\Qualifier;
use Rector\BetterPhpDocParser\PhpDoc\DoctrineAnnotationTagValueNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Core\Rector\AbstractRector;
use Rector\PhpAttribute\NodeFactory\PhpAttributeGroupFactory;
use ReflectionClass;
use ReflectionMethod;

use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use function array_merge;
use function assert;
use function explode;
use function get_class;
use function implode;
use function is_string;
use function property_exists;
use function str_contains;
use function substr;
use function trim;

final class AnnotationBindingRector extends AbstractRector
{
    private AnnotationReader $reader;
    public function __construct(
        private PhpAttributeGroupFactory $attributeGroupFactory,
        private PhpDocTagRemover $phpDocTagRemove
    ) {
        $this->reader = new AnnotationReader();
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('"Mehtod @named annotation will changed to be parameter #[Named] attribute"', [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @Named("a=foo,b=bar")
     * @Foo
     */
    public function __construct(int $a, int $b)
    {
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @Foo
     */
    public function __construct(#[Named('foo') int $a, #[Named('bar') int $b)
    {
    }
CODE_SAMPLE
        )]);
    }

    /**
     * @return string[]
     * @psalm-return array{0: ClassMethod::class}
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     *
     * @return ClassMethod|null
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof ClassMethod);

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        $namedNode = $this->processNodeAnnotation($phpDocInfo, $node);

        return $this->processQualiferAnnotation($phpDocInfo, $namedNode);
    }

    /** @return array<string, string> */
    private function parseName(string $name): array
    {
        $names = [];
        $keyValues = explode(',', $name);
        foreach ($keyValues as $keyValue) {
            $exploded = explode('=', $keyValue);
            if (isset($exploded[1])) {
                [$key, $value] = $exploded;
                assert(is_string($key));
                if (isset($key[0]) && $key[0] === '$') {
                    $key = substr($key, 1);
                }

                $names[trim($key)] = trim($value);
            }
        }

        return $names;
    }

    private function processNodeAnnotation(PhpDocInfo $phpDocInfo, ClassMethod $node): ClassMethod
    {
        $doctrineTagValueNode = $phpDocInfo->getByAnnotationClass(Named::class);
        if (! $doctrineTagValueNode instanceof DoctrineAnnotationTagValueNode) {
            return $node;
        }

        $nameString = $doctrineTagValueNode->getValuesWithSilentKey()[0]->value;
        if (str_contains($nameString, '=')) {
            return $this->convertEqualStringNamed($nameString, $node, $phpDocInfo, $doctrineTagValueNode);
        }

        return $this->convertSingleStringName($node, $nameString, $phpDocInfo, $doctrineTagValueNode);
    }

    private function convertEqualStringNamed(mixed $nameString, ClassMethod $node, PhpDocInfo $phpDocInfo, DoctrineAnnotationTagValueNode $doctrineTagValueNode): ClassMethod
    {
        $names = $this->parseName($nameString);
        foreach ($node->params as $param) {
            $varName = $param->var->name;
            if (! isset($names[$varName])) {
                continue;
            }

            $attrGroupsFromNamedAnnotation = $this->attributeGroupFactory->createFromClassWithItems(Named::class, [$names[$varName]]);
            $param->attrGroups = array_merge($param->attrGroups, [$attrGroupsFromNamedAnnotation]);

            $this->phpDocTagRemove->removeTagValueFromNode($phpDocInfo, $doctrineTagValueNode);
        }

        return $node;
    }

    private function convertSingleStringName(ClassMethod $node, mixed $nameString, PhpDocInfo $phpDocInfo, DoctrineAnnotationTagValueNode $doctrineTagValueNode): ClassMethod
    {
        $firstParam = $node->params[0];
        $attrGroupsFromNamedAnnotation = $this->attributeGroupFactory->createFromClassWithItems(Named::class, [$nameString]);
        $firstParam->attrGroups = array_merge($firstParam->attrGroups, [$attrGroupsFromNamedAnnotation]);
        $this->phpDocTagRemove->removeTagValueFromNode($phpDocInfo, $doctrineTagValueNode);

        return $node;
    }

    private function processQualiferAnnotation(PhpDocInfo $phpDocInfo, ClassMethod $node): ClassMethod
    {
        $nsParts = $node->getAttribute('parent')->namespacedName->parts;
        if ($nsParts === null) {
            return $node;
        }

        $class = implode('\\', $nsParts);
        $annotations = $this->reader->getMethodAnnotations(new ReflectionMethod($class, $node->name->name));
        $named = [];
        foreach ($annotations as $annotation) {
            $qualifier = $this->reader->getClassAnnotation(new ReflectionClass($annotation), Qualifier::class);
            $named = $this->getNamed($qualifier, $annotation, $named, $node->params);
        }

        foreach ($node->params as $param) {
            $varName = $param->var->name;
            if (! isset($named[$varName])) {
                continue;
            }

            $qualifier = $named[$varName];
            $attrGroupsFromNamedAnnotation = $this->attributeGroupFactory->createFromClass($qualifier);
            $param->attrGroups = array_merge($param->attrGroups, [$attrGroupsFromNamedAnnotation]);

            $doctrineTagValueNode = $phpDocInfo->getByAnnotationClass($qualifier);
            if ($doctrineTagValueNode instanceof DoctrineAnnotationTagValueNode) {
                $this->phpDocTagRemove->removeTagValueFromNode($phpDocInfo, $doctrineTagValueNode);
            }
        }

        return $node;
    }

    /** @param array<Node\Param> $params */
    private function getNamed(mixed $qualifier, object $annotation, array $named, array $params)
    {
        if (! $qualifier instanceof Qualifier) {
            return $named;
        }

        $annotationClass = get_class($annotation);
        if (property_exists($annotation, 'value')) {
            assert(property_exists($annotation, 'value'));
            $named[$annotation->value] = $annotationClass;

            return $named;
        }

        foreach ($params as $param) {
            $named[$param->var->name] = $annotationClass;
        }

        return $named;
    }
}
