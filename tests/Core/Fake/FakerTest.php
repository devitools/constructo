<?php

declare(strict_types=1);

namespace Constructo\Test\Core\Fake;

use Constructo\Core\Fake\Faker;
use Constructo\Core\Fake\Options;
use Constructo\Core\Serialize\Builder;
use Constructo\Support\Reflective\Notation;
use Constructo\Test\Stub\Builtin;
use Constructo\Test\Stub\EnumVariety;
use Constructo\Test\Stub\EnumerationAndNullable;
use PHPUnit\Framework\TestCase;

final class FakerTest extends TestCase
{
    public function testShouldCreateFakerWithDefaultParameters(): void
    {
        $faker = new Faker();

        $generator = $faker->generator();
        $this->assertNotNull($generator->name());
    }

    public function testShouldCreateFakerWithCustomLocale(): void
    {
        $faker = new Faker(locale: 'pt_BR');

        $generator = $faker->generator();
        $this->assertNotNull($generator->name());
    }

    public function testShouldCreateFakerWithCustomNotation(): void
    {
        $faker = new Faker(Notation::CAMEL);

        $result = $faker->fake(Builtin::class);

        $this->assertNotEmpty($result->toArray());
    }

    public function testShouldCreateFakerWithFormatters(): void
    {
        $formatters = [
            'string' => fn ($value) => strtoupper((string) $value),
        ];
        $faker = new Faker(formatters: $formatters);

        $generator = $faker->generator();
        $this->assertNotNull($generator->name());
    }

    public function testShouldGenerateFakeDataForBuiltinTypes(): void
    {
        $faker = new Faker();

        $result = $faker->fake(Builtin::class);

        $this->assertArrayHasKey('string', $result->toArray());
        $this->assertArrayHasKey('int', $result->toArray());
        $this->assertArrayHasKey('float', $result->toArray());
        $this->assertArrayHasKey('bool', $result->toArray());
        $this->assertArrayHasKey('array', $result->toArray());
        $this->assertIsString($result->get('string'));
        $this->assertIsInt($result->get('int'));
        $this->assertIsFloat($result->get('float'));
        $this->assertIsBool($result->get('bool'));
        $this->assertIsArray($result->get('array'));
    }

    public function testShouldGenerateFakeDataWithPresets(): void
    {
        $faker = new Faker();
        $presets = ['string' => 'custom_value'];

        $result = $faker->fake(Builtin::class, $presets);

        $this->assertEquals('custom_value', $result->get('string'));
        $this->assertIsInt($result->get('int'));
        $this->assertIsFloat($result->get('float'));
    }

    public function testShouldReturnEmptySetForClassWithoutParameters(): void
    {
        $faker = new Faker();
        $emptyClass = new class {
        };

        $result = $faker->fake($emptyClass::class);

        $this->assertEmpty($result->toArray());
    }

    public function testShouldGenerateDataUsingGenerateMethod(): void
    {
        $faker = new Faker();

        $name = $faker->generate('name');
        $email = $faker->generate('email');

        $this->assertIsString($name);
        $this->assertIsString($email);
        $this->assertStringContainsString('@', $email);
    }

    public function testShouldGenerateDataUsingMagicCallMethod(): void
    {
        $faker = new Faker();

        $name = $faker->name();
        $randomNumber = $faker->numberBetween(1, 100);

        $this->assertIsString($name);
        $this->assertIsInt($randomNumber);
        $this->assertGreaterThanOrEqual(1, $randomNumber);
        $this->assertLessThanOrEqual(100, $randomNumber);
    }

    public function testShouldReturnGeneratorInstance(): void
    {
        $faker = new Faker();

        $generator = $faker->generator();

        $this->assertNotNull($generator->name());
    }

    public function testShouldUseFrenchLocaleWhenProvided(): void
    {
        $faker = new Faker(locale: 'fr_FR');
        $generator = $faker->generator();

        $iban = $generator->iban('FR');
        $this->assertStringStartsWith('FR', $iban);

        $vat = $generator->vat();
        $this->assertStringStartsWith('FR ', $vat);

        $postcode = $generator->postcode();
        $this->assertMatchesRegularExpression('/^\d{5}$/', $postcode);

        $phone = $generator->phoneNumber();
        $this->assertMatchesRegularExpression('/^(\+33|0)[0-9\s\(\)\.]{8,}/', $phone);

        $companySuffix = $generator->companySuffix();
        $frenchSuffixes = [
            'SA',
            'SAS',
            'SARL',
            'S.A.',
            'et Fils',
            'S.A.S.',
            'S.A.R.L.',
        ];
        $this->assertContains($companySuffix, $frenchSuffixes);

        $timezone = $generator->timezone('FR');
        $this->assertEquals('Europe/Paris', $timezone);

        $foundFrenchDomain = false;
        for ($i = 0; $i < 10; $i++) {
            $domain = $generator->domainName();
            if (str_ends_with($domain, '.fr')) {
                $foundFrenchDomain = true;
                break;
            }
        }
        $this->assertTrue($foundFrenchDomain, 'Should generate at least one .fr domain');
    }

    public function testShouldUseBrazilianLocaleWhenProvided(): void
    {
        $faker = new Faker(locale: 'pt_BR');
        $generator = $faker->generator();

        $postcode = $generator->postcode();
        $this->assertMatchesRegularExpression('/^\d{5}-\d{3}$/', $postcode);

        $phone = $generator->phoneNumber();
        $this->assertMatchesRegularExpression('/^(\+55|\(?\d{2}\)?)\s?[0-9\s\-\(\)]{8,}/', $phone);

        $foundBrazilianDomain = false;
        for ($i = 0; $i < 10; $i++) {
            $domain = $generator->domainName();
            if (str_ends_with($domain, '.com.br') || str_ends_with($domain, '.br')) {
                $foundBrazilianDomain = true;
                break;
            }
        }
        $this->assertTrue($foundBrazilianDomain, 'Should generate at least one .br or .com.br domain');
    }

    public function testShouldUseDefaultLocaleWhenNoneProvided(): void
    {
        $faker = new Faker();
        $generator = $faker->generator();

        $name = $generator->name();
        $email = $generator->email();
        $address = $generator->address();

        $this->assertIsString($name);
        $this->assertNotEmpty($name);
        $this->assertIsString($email);
        $this->assertStringContainsString('@', $email);
        $this->assertIsString($address);
        $this->assertNotEmpty($address);
    }

    public function testShouldHandleEnumTypes(): void
    {
        $faker = new Faker();

        $result = $faker->fake(EnumVariety::class);

        $this->assertNotEmpty($result->toArray());
    }

    public function testShouldHandleComplexParameterResolution(): void
    {
        $faker = new Faker();
        $presets = [
            'string' => 'preset_string',
            'int' => 42,
        ];

        $result = $faker->fake(Builtin::class, $presets);

        $this->assertEquals('preset_string', $result->get('string'));
        $this->assertEquals(42, $result->get('int'));
        $this->assertIsFloat($result->get('float'));
        $this->assertIsBool($result->get('bool'));
        $this->assertIsArray($result->get('array'));
    }

    public function testShouldHandleNullableAndNotBackedEnum(): void
    {
        $faker = new Faker(ignoreFromDefaultValue: true);
        $builder = new Builder();

        $values = $faker->fake(EnumerationAndNullable::class);
        $instance = $builder->build(EnumerationAndNullable::class, $values);

        $this->assertSame($values->get('unit'), $instance->unit);
        $this->assertSame($values->get('backed'), $instance->backed->value);
        $this->assertSame($values->get('builtin')['string'], $instance->builtin->string);
    }

    public function testShouldGenerateWithArguments(): void
    {
        $faker = new Faker();

        $number = $faker->generate(
            'numberBetween',
            [
                10,
                20,
            ]
        );

        $this->assertIsInt($number);
        $this->assertGreaterThanOrEqual(10, $number);
        $this->assertLessThanOrEqual(20, $number);
    }
}
