<?php


/**
 * @see       https://github.com/laminas-api-tools/api-tools-hal for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-hal/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-hal/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\Hal\Extractor;

use ArrayObject;
use JsonSerializable;
use Laminas\ApiTools\Hal\EntityHydratorManager;
use Laminas\Hydrator\ExtractionInterface;
use SplObjectStorage;

/**
 * Extract entities.
 *
 * This version targets laminas-hydrator v3, and will be aliased to
 * Laminas\ApiTools\Hal\Extractor\EntityExtractor when that versions is in use.
 */
class EntityExtractorHydratorV3 implements ExtractionInterface
{
    /**
     * @var EntityHydratorManager
     */
    protected $entityHydratorManager;

    /**
     * Map of entities to their Laminas\ApiTools\Hal\Entity serializations
     *
     * @var SplObjectStorage
     */
    protected $serializedEntities;

    /**
     * @param EntityHydratorManager $entityHydratorManager
     */
    public function __construct(EntityHydratorManager $entityHydratorManager)
    {
        $this->entityHydratorManager = $entityHydratorManager;
        $this->serializedEntities = new SplObjectStorage();
    }

    /**
     * @inheritDoc
     */
    public function extract($entity): array
    {
        if (isset($this->serializedEntities[$entity])) {
            return $this->serializedEntities[$entity];
        }

        $this->serializedEntities[$entity] = $this->extractEntity($entity);

        return $this->serializedEntities[$entity];
    }

    private function extractEntity($entity): array
    {
        $hydrator = $this->entityHydratorManager->getHydratorForEntity($entity);

        if ($hydrator) {
            return $hydrator->extract($entity);
        }

        if ($entity instanceof JsonSerializable) {
            return $entity->jsonSerialize();
        }

        if ($entity instanceof ArrayObject) {
            return $entity->getArrayCopy();
        }

        return get_object_vars($entity);
    }
}
