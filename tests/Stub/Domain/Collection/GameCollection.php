<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Domain\Collection;

use Constructo\Test\Stub\Domain\Entity\Game;
use Constructo\Type\Collection;

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
