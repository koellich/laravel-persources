@extends('layouts/layoutMaster')

@section('title', 'List')

@section('content')

<h4>
    <span>{{ trans($resource->pluralName) }} list</span>
</h4>

<div>
    <table>
        <thead>
        <tr>
            @foreach($resource->listItemAttributes as $lia)
            <th>{{ trans($lia) }}</th>
            @endforeach
            <th>{{ trans('actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
        <tr>
            @foreach($resource->listItemAttributes as $lia)
            <td>{{ $item[$lia] }}</td>
            @endforeach
            <td>
                @foreach($resource->actions as $action)
                    @if($action !== 'create')
                    <a href="{{ $resource->getRoute($action, $item['id']) }}">{{ trans($action) }}</a>
                    @endif
                @endforeach
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection
