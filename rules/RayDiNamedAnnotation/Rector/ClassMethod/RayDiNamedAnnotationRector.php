<?php

declare (strict_types=1);

namespace Rector\Ray\RayDiNamedAnnotation\Rector\ClassMethod;

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
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use function array_merge;
use function assert;
use function explode;
use function get_class;
use function implode;
use function is_string;
use function property_exists;
use function substr;
use function trim;

/**
 * @see \Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\RayDiNamedAnnotationRectorTest
 */
final class RayDiNamedAnnotationRector extends AbstractRector
{
    private AnnotationReader $reader;
    public function __construct(
        private PhpAttributeGroupFactory $attributeGroupFactory,
        private PhpDocTagRemover $phpDocTagRemove
    ){
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
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof ClassMethod);

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        $namedNode = $this->processNodeAnnotation($phpDocInfo, $node);
        $qualifierNode = $this->processQualiferAnnotation($phpDocInfo, $namedNode);

        return $qualifierNode;
    }

    /**
     * @return array<string, string>
     */
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


    private function processNodeAnnotation(PhpDocInfo $phpDocInfo, ClassMethod $node): ?ClassMethod
    {
        $doctrineTagValueNode = $phpDocInfo->getByAnnotationClass(Named::class);
        if (!$doctrineTagValueNode instanceof DoctrineAnnotationTagValueNode) {
            return $node;
        }
        $nameString = $doctrineTagValueNode->getValuesWithSilentKey()[0]->value;
        $names = $this->parseName($nameString);
        foreach ($node->params as $param) {
            $varName = $param->var->name;
            if (!isset($names[$varName])) {
                continue;
            }
            $attrGroupsFromNamedAnnotation = $this->attributeGroupFactory->createFromClassWithItems(Named::class, [$names[$varName]]);
            $param->attrGroups = array_merge($param->attrGroups, [$attrGroupsFromNamedAnnotation]);

            $this->phpDocTagRemove->removeTagValueFromNode($phpDocInfo, $doctrineTagValueNode);
        }
        return $node;
    }

    private function processQualiferAnnotation(PhpDocInfo $phpDocInfo, ClassMethod $node): ?ClassMethod
    {
        $nsParts = $node->getAttribute('parent')->namespacedName->parts;
        $class = implode('\\', $nsParts);
        $annotations = $this->reader->getMethodAnnotations(new ReflectionMethod($class, $node->name->name));
        $named = [];
        foreach ($annotations as $annotation) {
            $qualifier = $this->reader->getClassAnnotation(new ReflectionClass($annotation), Qualifier::class);
            if ($qualifier instanceof Qualifier && property_exists($annotation, 'value')) {
                assert(property_exists($annotation, 'value'));
                $named[$annotation->value] = get_class($annotation);
            }
        }
        foreach ($node->params as $param) {
            $varName = $param->var->name;
            if (!isset($named[$varName])) {
                continue;
            }
            $qualifier = $named[$varName];
            $attrGroupsFromNamedAnnotation = $this->attributeGroupFactory->createFromClass($qualifier);
            $param->attrGroups = array_merge($param->attrGroups, [$attrGroupsFromNamedAnnotation]);

            $doctrineTagValueNode = $phpDocInfo->getByAnnotationClass($qualifier);

            $this->phpDocTagRemove->removeTagValueFromNode($phpDocInfo, $doctrineTagValueNode);
        }
        return $node;
    }
}
