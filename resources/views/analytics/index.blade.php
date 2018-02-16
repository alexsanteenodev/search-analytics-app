@extends('layouts.app')

@section('template_title')
  Showing Analytics
@endsection

@section('template_linked_css')

@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div id="embed-api-auth-container"></div></div>
        <div class="row">
            <div id="chart-1-container"></div></div>
        <div class="row">
            <div id="chart-2-container"></div>
        </div>
        <div class="row">
            <div id="chart-3-container"></div>
        </div>
        <div class="row">
            <div id="view-selector-1-container"></div>
        </div>
        <div class="row">
            <div id="view-selector-2-container"></div>
        </div>
    </div>

    <!-- Step 2: Load the library. -->

    <script>
        (function(w,d,s,g,js,fs){
            g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
            js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
            js.src='https://apis.google.com/js/platform.js';
            fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
        }(window,document,'script'));
    </script>
    <script>

        gapi.analytics.ready(function() {

            /**
             * Authorize the user immediately if the user has already granted access.
             * If no access has been created, render an authorize button inside the
             * element with the ID "embed-api-auth-container".
             */
            gapi.analytics.auth.authorize({
                container: 'embed-api-auth-container',
                clientid: '710532444284-ptvdd2h3s7r6htr7cnhiandju7ddlsik.apps.googleusercontent.com'
            });


            /**
             * Create a ViewSelector for the first view to be rendered inside of an
             * element with the id "view-selector-1-container".
             */
            var viewSelector1 = new gapi.analytics.ViewSelector({
                container: 'view-selector-1-container'
            });

            /**
             * Create a ViewSelector for the second view to be rendered inside of an
             * element with the id "view-selector-2-container".
             */
            var viewSelector2 = new gapi.analytics.ViewSelector({
                container: 'view-selector-2-container'
            });

            // Render both view selectors to the page.
            viewSelector1.execute();
            viewSelector2.execute();


            /**
             * Create the first DataChart for top countries over the past 30 days.
             * It will be rendered inside an element with the id "chart-1-container".
             */
            var dataChart1 = new gapi.analytics.googleCharts.DataChart({
                query: {

                    viewId: "168306386",
                    metrics: 'ga:sessions',
                    dimensions: 'ga:country',
                    'start-date': '30daysAgo',
                    'end-date': 'yesterday',
                    'max-results': 6,
                    sort: '-ga:sessions'
                },
                chart: {
                    container: 'chart-1-container',
                    type: 'PIE',
                    options: {
                        width: '100%',
                        pieHole: 4/9
                    }
                }
            });


            /**
             * Create the second DataChart for top countries over the past 30 days.
             * It will be rendered inside an element with the id "chart-2-container".
             */
            var dataChart2 = new gapi.analytics.googleCharts.DataChart({
                query: {

                    viewId: "168329090",
                    metrics: 'ga:sessions',
                    dimensions: 'ga:country',
                    'start-date': '30daysAgo',
                    'end-date': 'yesterday',
                    'max-results': 6,
                    sort: '-ga:sessions'
                },
                chart: {
                    container: 'chart-2-container',
                    type: 'PIE',
                    options: {
                        width: '100%',
                        pieHole: 4/9
                    }
                }
            });


            var  dateRange3 = {
                'start-date': '30daysAgo',
                'end-date': 'yesterday'
            };
            var commonConfig = {
                query: {
                    metrics: 'ga:sessions',
                    dimensions: 'ga:date'
                },
                chart: {
                    type: 'LINE',
                    options: {
                        width: '100%'
                    }
                }
            };

            var dataChart3 = new gapi.analytics.googleCharts.DataChart(commonConfig)
                .set({query: dateRange3})
                .set({chart: {container: 'chart-3-container'}});

            // viewSelector1.on('change', function(ids) {
            //     console.log(ids);
            //     dataChart1.set({query: {ids: 'ga:168306386'}}).execute();
            // });
            //
            // /**
            //  * Update the second dataChart when the second view selecter is changed.
            //  */
            //
            // viewSelector2.on('change', function(ids) {
            //     dataChart2.set({query: {ids: 'ga:168329090'}}).execute();
            // });


            viewSelector1.on('change', function(ids) {
                console.log(ids);
                dataChart3.set({query: {ids: ids}}).execute();
            });



        });
    </script>

@endsection

@section('template_scripts')

@endsection