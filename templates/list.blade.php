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
            <th>{{ $lia }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
        <tr>
            @foreach($resource->listItemAttributes as $lia)
            <td>{{ $item->{$lia} }}</td>
            @endforeach
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection
