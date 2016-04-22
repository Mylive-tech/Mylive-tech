$(function() {

    Morris.Area({
        element: 'morris-area-chart',
        data: [{
            period: '2010 Q1',
            total: 2666,
            redirect: null,
            broken: 2647
        }, {
            period: '2010 Q2',
            total: 2778,
            redirect: 2294,
            broken: 2441
        }, {
            period: '2010 Q3',
            total: 4912,
            redirect: 1969,
            broken: 2501
        }, {
            period: '2010 Q4',
            total: 3767,
            redirect: 3597,
            broken: 5689
        }, {
            period: '2011 Q1',
            total: 6810,
            redirect: 1914,
            broken: 2293
        }, {
            period: '2011 Q2',
            total: 5670,
            redirect: 4293,
            broken: 1881
        }, {
            period: '2011 Q3',
            total: 4820,
            redirect: 3795,
            broken: 1588
        }, {
            period: '2011 Q4',
            total: 15073,
            redirect: 5967,
            broken: 5175
        }, {
            period: '2012 Q1',
            total: 10687,
            redirect: 4460,
            broken: 2028
        }, {
            period: '2012 Q2',
            total: 8432,
            redirect: 5713,
            broken: 1791
        }],
        xkey: 'period',
        ykeys: ['total', 'redirect', 'broken'],
        labels: ['Total', '301', '404'],
        pointSize: 2,
        hideHover: 'auto',
        resize: true
    });

    Morris.Donut({
        element: 'morris-donut-chart',
        data: [{
            label: "Redirect (301)",
            value: 12
        }, {
            label: "Broken (404)",
            value: 30
        }],
        resize: true
    });

    Morris.Bar({
        element: 'morris-bar-chart',
        data: [{
            y: '2006',
            a: 100,
            b: 90
        }, {
            y: '2007',
            a: 75,
            b: 65
        }, {
            y: '2008',
            a: 50,
            b: 40
        }, {
            y: '2009',
            a: 75,
            b: 65
        }, {
            y: '2010',
            a: 50,
            b: 40
        }, {
            y: '2011',
            a: 75,
            b: 65
        }, {
            y: '2012',
            a: 100,
            b: 90
        }],
        xkey: 'y',
        ykeys: ['a', 'b'],
        labels: ['Series A', 'Series B'],
        hideHover: 'auto',
        resize: true
    });

});
