<?php

namespace Sulu\Component\Rest\Serializer;

use JMS\Serializer\JsonSerializationVisitor;

class CsvSerializationVisitor extends JsonSerializationVisitor
{
    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        $root = $this->getRoot();
        if ($root instanceof \ArrayObject) {
            $root = iterator_to_array($root);
        }

        return implode(';', array_values($root));
    }
}
