<?php

declare(strict_types=1);

namespace Morph\Test\Stub\Domain\Collection;

use Morph\Test\Stub\Domain\Entity\Game;
use Morph\Type\Collection;

/**
 * @extends Collection<Game>
 */
final class GameCollection extends Collection
{
    public function current(): Game
    {
        return $this->validate($this->datum());
    }

    protected function validate(mixed $datum): Game
    {
        return ($datum instanceof Game) ? $datum : throw $this->exception(Game::class, $datum);
    }
}
