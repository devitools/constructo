<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Domain\Collection\Game;

use Constructo\Contract\Collectable;
use Constructo\Test\Stub\Domain\Entity\Game\Feature;
use Constructo\Type\Collection;

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
