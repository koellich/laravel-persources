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
                @foreach($resource->getActionsForCurrentUser() as $action)
                    @if($action !== 'create' && $action !== 'update')
                    <button onclick="performAction({{ $item['id'] }}, '{{ $resource->getHttpMethod($action) }}')">
                        {{ trans($action) }}
                    </button>
                    @endif
                @endforeach
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
    function performAction(id, method) {
        console.log('performAction', id, method);
        let url = window.location + '/' + id;
        if (method === 'GET') {
            window.location = url;
        } else {
            fetch(url, {
                method: method
            }).then(
                (response) => alert(response.status + ' ' + response.statusText),
                (reason) => alert(reason));
        }
    }
</script>

@endsection
