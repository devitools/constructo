<?php

declare(strict_types=1);

namespace Constructo\Test\Type\Collection;

use Constructo\Test\Type\Collection\CollectionTestMockStub as Stub;
use Constructo\Type\Collection;

final class CollectionTestMock extends Collection
{
    public function unsafe(bool $strict): self
    {
        $this->unsafe = $strict;
        return $this;
    }

    public function current(): Stub
    {
        return $this->validate($this->datum());
    }

    protected function validate(mixed $datum): Stub
    {
        if ($datum instanceof Stub) {
            return $datum;
        }
        throw $this->exception(Stub::class, $datum);
    }
}
