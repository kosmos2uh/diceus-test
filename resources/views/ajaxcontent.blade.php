
<div class="row">
    <div class="col-sm-6">
        @include('teamstable')
        <div class="row">
            <div class="col-sm-6">
                @if($week < 6)
                    <a href="#" class="btn btn-primary" id="play_all">Play all</a>
                @endif
            </div>
            <div class="col-sm-6 text-right">
                @if($week == 6)
                    <a href="#" class="btn btn-warning" id="reset_button">Reset</a>
                @else
                    <a href="#" class="btn btn-info" id="next_week">Next Week</a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        @include('resultstable')
    </div>
</div>

