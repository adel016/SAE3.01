<?php

namespace App\Covoiturage\Model\DataObject;

abstract class AbstractDataObject {
    abstract public function formatTableau(): array;
}
