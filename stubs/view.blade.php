@extends('layouts/layoutMaster')

@section('title', 'List')

@section('content')

<h4>
    <span>{{ trans($resource->singularName) }}</span>
</h4>

<div>
    <table>
        <tr>
            @foreach($resource->getActionsForCurrentUser() as $action)
            @if($action === 'update' || $action === 'delete')
            <td>
                <button onclick="performAction('{{ $resource->getHttpMethod($action) }}')">
                    {{ trans($action) }}
                </button>
            </td>
            @endif
            @endforeach
        </tr>
    </table>
</div>

<div>
    <table>
        <thead></thead>
        <tbody>
        @foreach($resource->singleItemAttributes as $sia)
        <tr>
            <th role="row">{{ trans($sia) }}</th>
            <td>{{ $item[$sia] }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
    function performAction(method) {
        console.log('performAction', method);
        let url = window.location;
        if (method === 'GET') {
            window.location = url;
        } else {
            fetch(url, {
                method: method,
            }).then(
                (response) => alert(response.status + ' ' + response.statusText),
                (reason) => alert(reason));
        }
    }
</script>

@endsection
