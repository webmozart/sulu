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
        $result = @json_encode($this->getRoot(), $this->getOptions());

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $result;

            case JSON_ERROR_UTF8:
                throw new \RuntimeException('Your data could not be encoded because it contains invalid UTF8 characters.');

            default:
                throw new \RuntimeException(sprintf('An error occurred while encoding your data (error code %d).', json_last_error()));
        }
    }
}
