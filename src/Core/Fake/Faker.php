<?php

declare(strict_types=1);

namespace Constructo\Core\Fake;

use Constructo\Contract\Formatter;
use Constructo\Contract\Testing\Faker as Contract;
use Constructo\Core\Fake\Resolver\FromCollection;
use Constructo\Core\Fake\Resolver\FromDefaultValue;
use Constructo\Core\Fake\Resolver\FromDependency;
use Constructo\Core\Fake\Resolver\FromEnum;
use Constructo\Core\Fake\Resolver\FromPreset;
use Constructo\Core\Fake\Resolver\FromTypeAttributes;
use Constructo\Core\Fake\Resolver\FromTypeBuiltin;
use Constructo\Core\Fake\Resolver\FromTypeDate;
use Constructo\Support\Reflective\Engine;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Support\Reflective\Notation;
use Constructo\Support\Set;
use DateTime;
use Faker\Factory;
use Faker\Generator;
use ReflectionException;
use ReflectionParameter;

use function Constructo\Cast\stringify;
use function getenv;

/**
 * // phpcs:disable Generic.Files.LineLength
 * @method string citySuffix()
 * @method string streetSuffix()
 * @method string buildingNumber()
 * @method string city()
 * @method string streetName()
 * @method string streetAddress()
 * @method string postcode()
 * @method string address()
 * @method string country()
 * @method float latitude($min = -90, $max = 90)
 * @method float longitude($min = -180, $max = 180)
 * @method float[] localCoordinates()
 * @method int randomDigitNotNull()
 * @method mixed passthrough($value)
 * @method string randomLetter()
 * @method string randomAscii()
 * @method array randomElements($array = ['a', 'b', 'c'], $count = 1, $allowDuplicates = false)
 * @method mixed randomElement($array = ['a', 'b', 'c'])
 * @method int|string|null randomKey($array = [])
 * @method array|string shuffle($arg = '')
 * @method array shuffleArray($array = [])
 * @method string shuffleString($string = '', $encoding = 'UTF-8')
 * @method string numerify($string = '###')
 * @method string lexify($string = '????')
 * @method string bothify($string = '## ??')
 * @method string asciify($string = '****')
 * @method string regexify($regex = '')
 * @method string toLower($string = '')
 * @method string toUpper($string = '')
 * @method int biasedNumberBetween($min = 0, $max = 100, $function = 'sqrt')
 * @method string hexColor()
 * @method string safeHexColor()
 * @method array rgbColorAsArray()
 * @method string rgbColor()
 * @method string rgbCssColor()
 * @method string rgbaCssColor()
 * @method string safeColorName()
 * @method string colorName()
 * @method string hslColor()
 * @method array hslColorAsArray()
 * @method string company()
 * @method string companySuffix()
 * @method string jobTitle()
 * @method int unixTime($max = 'now')
 * @method DateTime dateTime($max = 'now', $timezone = null)
 * @method DateTime dateTimeAD($max = 'now', $timezone = null)
 * @method string iso8601($max = 'now')
 * @method string date($format = 'Y-m-d', $max = 'now')
 * @method string time($format = 'H:i:s', $max = 'now')
 * @method DateTime dateTimeBetween($startDate = '-30 years', $endDate = 'now', $timezone = null)
 * @method DateTime dateTimeInInterval($date = '-30 years', $interval = '+5 days', $timezone = null)
 * @method DateTime dateTimeThisCentury($max = 'now', $timezone = null)
 * @method DateTime dateTimeThisDecade($max = 'now', $timezone = null)
 * @method DateTime dateTimeThisYear($max = 'now', $timezone = null)
 * @method DateTime dateTimeThisMonth($max = 'now', $timezone = null)
 * @method string amPm($max = 'now')
 * @method string dayOfMonth($max = 'now')
 * @method string dayOfWeek($max = 'now')
 * @method string month($max = 'now')
 * @method string monthName($max = 'now')
 * @method string year($max = 'now')
 * @method string century()
 * @method string timezone($countryCode = null)
 * @method void setDefaultTimezone($timezone = null)
 * @method string getDefaultTimezone()
 * @method string file($sourceDirectory = '/tmp', $targetDirectory = '/tmp', $fullPath = true)
 * @method string randomHtml($maxDepth = 4, $maxWidth = 4)
 * @codingStandardsIgnoreLine
 * @method string imageUrl($width = 640, $height = 480, $category = null, $randomize = true, $word = null, $gray = false, string $format = 'png')
 * @codingStandardsIgnoreLine
 * @method string image($dir = null, $width = 640, $height = 480, $category = null, $fullPath = true, $randomize = true, $word = null, $gray = false, string $format = 'png')
 * @method string email()
 * @method string safeEmail()
 * @method string freeEmail()
 * @method string companyEmail()
 * @method string freeEmailDomain()
 * @method string safeEmailDomain()
 * @method string userName()
 * @method string password($minLength = 6, $maxLength = 20)
 * @method string domainName()
 * @method string domainWord()
 * @method string tld()
 * @method string url()
 * @method string slug($nbWords = 6, $variableNbWords = true)
 * @method string ipv4()
 * @method string ipv6()
 * @method string localIpv4()
 * @method string macAddress()
 * @method string word()
 * @method array|string words($nb = 3, $asText = false)
 * @method string sentence($nbWords = 6, $variableNbWords = true)
 * @method array|string sentences($nb = 3, $asText = false)
 * @method string paragraph($nbSentences = 3, $variableNbSentences = true)
 * @method array|string paragraphs($nb = 3, $asText = false)
 * @method string text($maxNbChars = 200)
 * @method bool boolean($chanceOfGettingTrue = 50)
 * @method string md5()
 * @method string sha1()
 * @method string sha256()
 * @method string countryCode()
 * @method string countryISOAlpha3()
 * @method string languageCode()
 * @method string currencyCode()
 * @method string emoji()
 * @method string creditCardType()
 * @method string creditCardNumber($type = null, $formatted = false, $separator = '-')
 * @method DateTime creditCardExpirationDate($valid = true)
 * @method string creditCardExpirationDateString($valid = true, $expirationDateFormat = null)
 * @method array creditCardDetails($valid = true)
 * @method string iban($countryCode = null, $prefix = '', $length = null)
 * @method string swiftBicNumber()
 * @method string name($gender = null)
 * @method string firstName($gender = null)
 * @method string firstNameMale()
 * @method string firstNameFemale()
 * @method string lastName($gender = null)
 * @method string title($gender = null)
 * @method string titleMale()
 * @method string titleFemale()
 * @method string phoneNumber()
 * @method string e164PhoneNumber()
 * @method int imei()
 * @method string realText($maxNbChars = 200, $indexSize = 2)
 * @method string realTextBetween($minNbChars = 160, $maxNbChars = 200, $indexSize = 2)
 * @method string macProcessor()
 * @method string linuxProcessor()
 * @method string userAgent()
 * @method string chrome()
 * @method string msedge()
 * @method string firefox()
 * @method string safari()
 * @method string opera()
 * @method string internetExplorer()
 * @method string windowsPlatformToken()
 * @method string macPlatformToken()
 * @method string iosMobileToken()
 * @method string linuxPlatformToken()
 * @method string uuid()
 * @method string mimeType()
 * @method string fileExtension()
 * @method string filePath()
 * @method string bloodType()
 * @method string bloodRh()
 * @method string bloodGroup()
 * @method string ean13()
 * @method string ean8()
 * @method string isbn10()
 * @method string isbn13()
 * @method int numberBetween($int1 = 0, $int2 = 2147483647)
 * @method int randomDigit()
 * @method int randomDigitNot($except)
 * @method int randomDigitNotZero()
 * @method float randomFloat($nbMaxDecimals = null, $min = 0, $max = null)
 * @method int randomNumber($nbDigits = null, $strict = false)
 * @method string semver(bool $preRelease = false, bool $build = false)
 * @method string vat($spacedNationalPrefix = true)
 * // phpcs:enable Generic.Files.LineLength
 */
class Faker extends Engine implements Contract
{
    protected readonly Generator $generator;

    /**
     * @param array<callable|Formatter> $formatters
     * @SuppressWarnings(StaticAccess)
     */
    public function __construct(
        Notation $case = Notation::SNAKE,
        array $formatters = [],
        ?string $locale = null,
    ) {
        parent::__construct($case, $formatters);

        $this->generator = Factory::create($this->locale($locale));
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->generate($name, $arguments);
    }

    /**
     * @template U of object
     * @param class-string<U> $class
     * @throws ReflectionException
     */
    public function fake(string $class, array $presets = []): Set
    {
        $target = Target::createFrom($class);
        $parameters = $target->getReflectionParameters();
        if (empty($parameters)) {
            return Set::createFrom([]);
        }

        return $this->resolveParameters($parameters, new Set($presets));
    }

    public function generate(string $name, array $arguments = []): mixed
    {
        return $this->generator->__call($name, $arguments);
    }

    public function generator(): Generator
    {
        return $this->generator;
    }

    /**
     * @param array<ReflectionParameter> $parameters
     */
    private function resolveParameters(array $parameters, Set $presets): Set
    {
        $values = [];
        foreach ($parameters as $parameter) {
            $field = $this->casedField($parameter);
            $generated = (new FromDependency($this->notation, $this->formatters))
                ->then(new FromTypeDate($this->notation, $this->formatters))
                ->then(new FromCollection($this->notation, $this->formatters))
                ->then(new FromTypeBuiltin($this->notation, $this->formatters))
                ->then(new FromTypeAttributes($this->notation, $this->formatters))
                ->then(new FromEnum($this->notation, $this->formatters))
                ->then(new FromDefaultValue($this->notation, $this->formatters))
                ->then(new FromPreset($this->notation, $this->formatters))
                ->resolve($parameter, $presets);

            if ($generated === null) {
                continue;
            }
            $values[$field] = $generated->content;
        }
        return Set::createFrom($values);
    }

    private function locale(?string $locale): string
    {
        $fallback = static function (string $default = 'en_US'): string {
            $locale = stringify(getenv('FAKER_LOCALE'), $default);
            return empty($locale)
                ? $default
                : $locale;
        };
        return $locale ?? $fallback();
    }
}
