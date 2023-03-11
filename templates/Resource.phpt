<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Koellich\Persources\Facades\Resource;

class %RESOURCE% extends Resource
{
    $model = %MODEL%;

    $singularName = %SINGULAR_NAME%;

    $pluralName = %PLURAL_NAME%;

    $permissions = %PERMISSIONS%;

    $listItemAttributes = %LISTITEM_ATTRIBUTES%;

    $singleItemAttributes = %SINGLEITEM_ATTRIBUTES%;
}