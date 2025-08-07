<?php

declare(strict_types=1);

namespace Constructo\Test\Examples;

use Constructo\Core\Serialize\Builder;
use Constructo\Support\Set;
use DateTime;
use PHPUnit\Framework\TestCase;

class BuilderExamplesTest extends TestCase
{
    public function testShouldBuildUserBasic(): void
    {
        $set = Set::createFrom([
            'id' => 1,
            'name' => 'João Silva',
            'is_active' => true,
            'tags' => [
                'nice',
                'welcome',
            ],
            'birth_date' => '1981-08-13',
        ]);

        $user = (new Builder())->build(UserBasic::class, $set);

        $this->assertInstanceOf(UserBasic::class, $user);
        $this->assertSame(1, $user->id);
        $this->assertSame('João Silva', $user->name);
        $this->assertTrue($user->isActive);
        $this->assertSame(
            [
                'nice',
                'welcome',
            ],
            $user->tags
        );
        $this->assertInstanceOf(DateTime::class, $user->birthDate);
        $this->assertSame('1981-08-13', $user->birthDate->format('Y-m-d'));
    }
}

readonly class UserBasic
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $isActive = true,
        public array $tags = [],
        public DateTime $birthDate = new DateTime(),
    ) {
    }
}
