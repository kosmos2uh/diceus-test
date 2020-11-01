<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Champions League</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <style>
            body {
                font-family: 'Arial';
            }
            .team_table th,
            .team_table td{
                text-align: center;
            }
            .team_table tr th:first-child,
            .team_table tr td:first-child{
                text-align: left;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                @section('h1')
                <h1>Champions League</h1>
                @endsection
                @yield('h1')
            </div>
        </div>
    </div>
    @yield('content')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
{{--    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>--}}
{{--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>--}}
    <script>
        $(document).ready(function(){
            const container = $("#content_container");
            const next_week_btn = $("#next_week");
            const play_all_btn = $("#play_all");
            const reset_btn = $("#reset_button");
            container.on('click', "#next_week", function(){
                $.ajax({
                    url: '{{ route('next') }}',
                    type: "get",
                    dataType: "script",
                    data: {},
                    beforeSend : function(){
                        next_week_btn.prop("disabled", true).text('loading');
                    },
                    complete : function(){
                        next_week_btn.prop("disabled", false).text('Next Week');
                    },
                    success : function(data){
                        container.html(data);
                    }
                });
                return false;
            }).on('click', "#reset_button", function(){
                $.ajax({
                    url: '{{ route('resetLeague') }}',
                    type: "get",
                    dataType: "script",
                    data: {},
                    beforeSend : function(){
                        reset_btn.prop("disabled", true).text('loading');
                    },
                    complete : function(){
                        reset_btn.prop("disabled", false).text('Reset');
                    },
                    success : function(data){
                        container.html(data);
                    }
                });
                return false;
            }).on('click', "#play_all", function(){
                $.ajax({
                    url: '{{ route('playall') }}',
                    type: "get",
                    dataType: "script",
                    data: {},
                    beforeSend : function(){
                        play_all_btn.prop("disabled", true).text('loading');
                    },
                    complete : function(){
                        play_all_btn.prop("disabled", false).text('Play all');
                    },
                    success : function(data){
                        container.html(data);
                    }
                });
                return false;
            });
        });
    </script>
    </body>
</html>
