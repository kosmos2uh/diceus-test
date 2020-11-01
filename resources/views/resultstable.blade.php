@foreach($results as $week_number => $week_results)
<h3 class="text-center">Results of Week {{ $week_number }}</h3>
<table class="table table-bordered table-striped">
    @foreach($week_results as $result)
        <tr>
            <td class="text-right">{{ $result['team_home'] }}</td>
            @if($result['finished'])
                <td class="text-center"><a href="{{ route('game.edit', ['game' => $result['id']]) }}">{{ $result['goals_home'] }} : {{ $result['goals_visitor'] }}</a></td>
            @else
                <td class="text-center">? : ?</td>
            @endif
            <td>{{ $result['team_visitor'] }}</td>
        </tr>
    @endforeach
</table>
@endforeach
