<?php

declare(strict_types=1);

namespace Morph\Test\Stub\Domain\Collection\Game;

use Morph\Contract\Collectable;
use Morph\Test\Stub\Domain\Entity\Game\Feature;
use Morph\Type\Collection;

/**
 * @extends Collectable<Feature>
 */
class FeatureCollection extends Collection
{
    public function current(): Feature
    {
        return $this->validate($this->datum());
    }

    protected function validate(mixed $datum): Feature
    {
        return ($datum instanceof Feature) ? $datum : throw $this->exception(Feature::class, $datum);
    }
}
