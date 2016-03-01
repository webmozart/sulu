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

        $lines = [];
        if (count($root) > 0) {
            $lines[] = implode(';', array_keys($root[0]));
        }

        foreach ($root as $item) {
            $lines[] = implode(';', array_values($item));
        }

        return implode(PHP_EOL, $lines);
    }
}
