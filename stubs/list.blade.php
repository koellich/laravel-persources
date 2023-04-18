<html>
    <head>
        <title>{{ trans($resource->pluralName) }} list</title>
    </head>
    <body>
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
                @foreach($resource->getItems() as $item)
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
    </body>
</html>