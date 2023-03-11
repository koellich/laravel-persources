@extends('layouts/layoutMaster')

@section('title', 'List')

@section('content')

<h4>
    <span>{{ trans($resource->singularName) }}</span>
</h4>

<div>
    <table>
        <thead></thead>
        <tbody>
            @foreach($resource->singleItemAttributes as $sia)
            <tr>
                <th role="row">{{ $sia }}</th>
                <td>{{ $item->{$sia} }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
