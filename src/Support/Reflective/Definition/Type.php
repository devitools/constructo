<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Definition;

enum Type: string
{
    case IP_V4 = 'ipv4';
    case IP_V6 = 'ipv6';
    case EMAIL = 'email';
    case NAME = 'name';
    case TITLE = 'title';
    case PHONE_NUMBER = 'phoneNumber';
    case IBAN = 'iban';
    case SWIFT_NUMBER = 'swiftBicNumber';
    case FIRST_NAME = 'firstName';
    case LAST_NAME = 'lastName';
    case CITY = 'city';
    case STREET = 'streetName';
    case ADDRESS = 'address';
    case COUNTRY = 'country';
    case POSTCODE = 'postcode';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case COMPANY = 'company';
    case JOB_TITLE = 'jobTitle';
    case USERNAME = 'userName';
    case PASSWORD = 'password';
    case DOMAIN = 'domainName';
    case TOP_LEVEL_DOMAIN = 'tld';
    case URL = 'url';
    case SLUG = 'slug';
    case WORD = 'word';
    case WORDS = 'words';
    case SENTENCE = 'sentence';
    case SENTENCES = 'sentences';
    case PARAGRAPH = 'paragraph';
    case PARAGRAPHS = 'paragraphs';
    case TEXT = 'text';
    case LOCALE = 'locale';
    case COUNTRY_CODE = 'countryCode';
    case LANGUAGE_CODE = 'languageCode';
    case CURRENCY_CODE = 'currencyCode';
    case EMOJI = 'emoji';
    case CREDIT_CARD_TYPE = 'creditCardType';
    case CREDIT_CARD_NUMBER = 'creditCardNumber';
    case CREDIT_CARD_EXPIRATION = 'creditCardExpirationDateString';
    case CREDIT_CARD_DETAILS = 'creditCardDetails';
    case IMEI = 'imei';
    case MAC_ADDRESS = 'macAddress';
    case USERAGENT = 'userAgent';
    case UUID = 'uuid';
}
