<?php

declare(strict_types=1);

namespace Constructo\Test\Stub\Reflector;

use Constructo\Type\Collection;

/**
 * @extends Collection<Node>
 */
final class NodeCollection extends Collection
{
    public function current(): Node
    {
        return $this->validate($this->datum());
    }

    protected function validate(mixed $datum): Node
    {
        return ($datum instanceof Node) ? $datum : throw $this->exception(Node::class, $datum);
    }
}
