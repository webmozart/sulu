<?php

namespace Sulu\Component\Rest\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\SerializerInterface;
use Sulu\Component\Rest\ListBuilder\ListRepresentation;

class ListRepresentationCsvHandler implements SubscribingHandlerInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'csv',
                'type' => ListRepresentation::class,
                'method' => 'doSerialize',
            ],
        ];
    }

    public function doSerialize(
        CsvSerializationVisitor $visitor,
        ListRepresentation $object,
        array $type,
        Context $context
    ) {
        // type array do not serialize "_embedded" items
        $array = json_decode($this->serializer->serialize($object, 'json'), true);

        return $context->accept($array['_embedded'][$object->getRel()]);
    }
}
