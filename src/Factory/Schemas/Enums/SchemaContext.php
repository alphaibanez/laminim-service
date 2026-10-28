<?php

namespace Lkt\Factory\Schemas\Enums;

enum SchemaContext: int
{
    case DatabaseData = 1;
    case CodedData = 2;
    case DatabaseView = 3;
    case ReadOnlyDatabaseData = 4;
    case DynamicDataSource = 5;
    case DynamicDataSourcePointer = 6;
}
