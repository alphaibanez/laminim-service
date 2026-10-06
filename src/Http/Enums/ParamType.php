<?php

namespace Lkt\Http\Enums;

enum ParamType: string
{
    case NotDefined = 'not-defined';
    case String = 'string';
    case Number = 'number';
    case Array = 'array';
    case Object = 'object';
    case Boolean = 'boolean';
    case PWAUUID = 'pwa:uuid';
    case PWAPlatform = 'pwa:platform';
    case PWAVersion = 'pwa:version';
}