<div style="margin-left: 25px; margin-top:150px; margin-right: 25px; ">
    <div>
        List of Application that status change from "<strong>{{ $from_status }}</strong>" to "<strong>{{ $to_status }}</strong>" :
    </div>
    <ul>
        @foreach($apps as $app)
            <li>{{ $app->name }} ({{ $app->uid }})</li>
        @endforeach
    </ul>
</div>
