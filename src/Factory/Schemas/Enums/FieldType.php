<?php

namespace Lkt\Factory\Schemas\Enums;

enum FieldType: string
{
    case Abstract = 'abstract';
    case Boolean = 'boolean';
    case Color = 'color';
    case Concat = 'concat';
    case ConstantValue = 'constant-value';
    case DateTime = 'datetime';
    case Email = 'email';
    case Encrypt = 'encrypt';
    case File = 'file';
    case Float = 'float';
    case ForeignKey = 'foreign-key';
    case ForeignKeys = 'foreign-keys';
    case HTML = 'html';
    case Id = 'id';
    case Image = 'image';
    case IntegerChoice = 'integer-choice';
    case Integer = 'integer';
    case JSON = 'json';
    case MethodGetter = 'method-getter';
    case Pivot = 'pivot';
//    case PivotLeftId = 'pivot-left-id';
//    case PivotRightId = 'pivot-right-id';
//    case PivotPosition = 'pivot-position';
    case Related = 'related';
    case RelatedKeys = 'related-keys';
    case RelatedKeysMerge = 'related-keys-merge';
    case SingleRelated = 'single-related';
    case StringChoice = 'string-choice';
    case String = 'string';
    case UnixTimeStamp = 'unix-timestamp';
    case Url = 'url';
    case ValueList = 'value-list';
}
