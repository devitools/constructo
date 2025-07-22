<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective;

/**
 * |------------------|----------------------|
 * | Name             | Example              |
 * |------------------|----------------------|
 * | 🐪 Camel case    | myNameIsBond         |
 * | 👨‍🏫 Pascal case   | MyNameIsBond         |
 * | 🐍 Snake case    | my_name_is_bond      |
 * | 👩‍🏫 Ada case      | My_Name_Is_Bond      |
 * | Ⓜ️ Macro case    | MY_NAME_IS_BOND      |
 * | 🥙 Kebab case    | my-name-is-bond      |
 * | 🚂 Train case    | My-Name-Is-Bond      |
 * | 🏦 Cobol case    | MY-NAME-IS-BOND      |
 * | 🔡 Lower case    | my name is bond      |
 * | 🔠 Upper case    | MY NAME IS BOND      |
 * | 📰 Title case    | My Name Is Bond      |
 * | ✍️ Sentence case | My name is bond      |
 * | ⚙️ Dot notation  | my.name.is.bond      |
 * |------------------|----------------------|
 */
enum Notation
{
    case CAMEL;
    case PASCAL;
    case SNAKE;
    case ADA;
    case MACRO;
    case KEBAB;
    case TRAIN;
    case COBOL;
    case LOWER;
    case UPPER;
    case TITLE;
    case SENTENCE;
    case DOT;
    case NONE;
}
