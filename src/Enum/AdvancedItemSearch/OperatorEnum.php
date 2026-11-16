<?php

declare(strict_types=1);

namespace App\Enum\AdvancedItemSearch;

use App\Enum\DatumTypeEnum;

class OperatorEnum
{
    public const string OPERATOR_EQUAL = 'equal';

    public const string OPERATOR_NOT_EQUAL = 'not-equal';

    public const string OPERATOR_CONTAINS = 'contains';

    public const string OPERATOR_DOES_NOT_CONTAIN = 'does-not-contain';

    public const string OPERATOR_SUPERIOR = 'superior';

    public const string OPERATOR_SUPERIOR_OR_EQUAL = 'superior-or-equal';

    public const string OPERATOR_INFERIOR = 'inferior';

    public const string OPERATOR_INFERIOR_OR_EQUAL = 'inferior-or-equal';

    public const string OPERATOR_EMPTY = 'empty';

    public const string OPERATOR_NOT_EMPTY = 'not-empty';

    public const string OPERATOR_EXISTS = 'exists';

    public const string OPERATOR_DOES_NOT_EXIST = 'does-not-exist';

    public static function getOperatorLabels(): array
    {
        return [
            self::OPERATOR_EQUAL => 'global.advanced_item_search.operator.equal',
            self::OPERATOR_NOT_EQUAL => 'global.advanced_item_search.operator.not_equal',
            self::OPERATOR_CONTAINS => 'global.advanced_item_search.operator.contains',
            self::OPERATOR_DOES_NOT_CONTAIN => 'global.advanced_item_search.operator.does_not_contain',
            self::OPERATOR_SUPERIOR => 'global.advanced_item_search.operator.superior',
            self::OPERATOR_SUPERIOR_OR_EQUAL => 'global.advanced_item_search.operator.superior_or_equal',
            self::OPERATOR_INFERIOR => 'global.advanced_item_search.operator.inferior',
            self::OPERATOR_INFERIOR_OR_EQUAL => 'global.advanced_item_search.operator.inferior_or_equal',
            self::OPERATOR_EMPTY => 'global.advanced_item_search.operator.empty',
            self::OPERATOR_NOT_EMPTY => 'global.advanced_item_search.operator.not_empty',
            self::OPERATOR_EXISTS => 'global.advanced_item_search.operator.exists',
            self::OPERATOR_DOES_NOT_EXIST => 'global.advanced_item_search.operator.does_not_exist',
        ];
    }

    public static function getLabelFromName(string $name): string
    {
        return self::getOperatorLabels()[$name];
    }

    public static function getOperatorsByType(string $type): array
    {
        $operators = match ($type) {
            DatumTypeEnum::TYPE_TEXT, DatumTypeEnum::TYPE_TEXTAREA, DatumTypeEnum::TYPE_MARKDOWN, DatumTypeEnum::TYPE_LINK, 'item_name' => [
                OperatorEnum::OPERATOR_EQUAL => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_EQUAL),
                OperatorEnum::OPERATOR_NOT_EQUAL => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_NOT_EQUAL),
                OperatorEnum::OPERATOR_CONTAINS => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_CONTAINS),
                OperatorEnum::OPERATOR_DOES_NOT_CONTAIN => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_DOES_NOT_CONTAIN),
            ],
            DatumTypeEnum::TYPE_COUNTRY, DatumTypeEnum::TYPE_CHECKBOX, 'collection_name', 'tag_name' => [
                OperatorEnum::OPERATOR_EQUAL => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_EQUAL),
                OperatorEnum::OPERATOR_NOT_EQUAL => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_NOT_EQUAL)
            ],
            DatumTypeEnum::TYPE_DATE, DatumTypeEnum::TYPE_NUMBER, DatumTypeEnum::TYPE_RATING => [
                OperatorEnum::OPERATOR_EQUAL => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_EQUAL),
                OperatorEnum::OPERATOR_NOT_EQUAL => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_NOT_EQUAL),
                OperatorEnum::OPERATOR_SUPERIOR => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_SUPERIOR),
                OperatorEnum::OPERATOR_SUPERIOR_OR_EQUAL => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_SUPERIOR_OR_EQUAL),
                OperatorEnum::OPERATOR_INFERIOR => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_INFERIOR),
                OperatorEnum::OPERATOR_INFERIOR_OR_EQUAL => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_INFERIOR_OR_EQUAL),
            ],
            DatumTypeEnum::TYPE_LIST, DatumTypeEnum::TYPE_CHOICE_LIST => [
                OperatorEnum::OPERATOR_CONTAINS => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_CONTAINS),
                OperatorEnum::OPERATOR_DOES_NOT_CONTAIN => OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_DOES_NOT_CONTAIN),
            ],
            default => []
        };

        if ($type !== 'item_name' && $type !== 'collection_name' && $type !== 'tag_name') {
            $operators[OperatorEnum::OPERATOR_EMPTY] = OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_EMPTY);
            $operators[OperatorEnum::OPERATOR_NOT_EMPTY] = OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_NOT_EMPTY);
            $operators[OperatorEnum::OPERATOR_EXISTS] = OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_EXISTS);
            $operators[OperatorEnum::OPERATOR_DOES_NOT_EXIST] = OperatorEnum::getLabelFromName(OperatorEnum::OPERATOR_DOES_NOT_EXIST);
        }

        return $operators;
    }
}
