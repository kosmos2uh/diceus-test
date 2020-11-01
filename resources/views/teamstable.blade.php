<table class="table table-bordered table-striped team_table">
    <tr>
        <th>Teams</th>
        <th>PTS</th>
        <th>P</th>
        <th>W</th>
        <th>D</th>
        <th>L</th>
        <th>GD</th>
    </tr>
    @foreach($teams as $team)
        <tr>
            <td>{{ $team->name }}</td>
            <td>{{ $team->points }}</td>
            <td>{{ $team->played }}</td>
            <td>{{ $team->win }}</td>
            <td>{{ $team->draw }}</td>
            <td>{{ $team->lose }}</td>
            <td>{{ $team->scored - $team->conceded }}</td>
        </tr>
    @endforeach
</table>
