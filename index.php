<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Prejudicial Appeals - Agenda</title>
	<link rel="stylesheet" type="text/css" href="static/kendo.common.min.css">
	<link rel="stylesheet" type="text/css" href="static/kendo.default.min.css">
	<script src="static/jquery.min.js"></script>
	<script src="static/kendo.all.min.js"></script>
	<script src="static/jszip.min.js"></script>
	<script src="static/kendo.web.min.js"></script>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	
</head>

<body>
<br>

<div id="team-schedule">
    <div id="people">
    	<label for="alex">Anonymization of Judgements</label>
        <input type="checkbox" id="alex" value="1">
        <label for="alex">Entry of Appeals</label>
        <input type="checkbox" id="bob" value="2">
        <label for="alex">Assistance of Appeals</label>
        <input type="checkbox" id="charlie" value="3">
    </div>
</div>


<br>

<?php

require 'datasource.php';


$resource = new \Kendo\UI\SchedulerResource();
$resource->field('ownerId')
    ->title('Owner')
    ->dataSource(array(
            array('text'=> 'Anonymization of Judgements', 'value' => 1, 'color' => '#ef701d'),
            array('text'=> 'Entry of Appeals', 'value' => 2, 'color' => '#5fb1f7'),
            array('text'=> 'Assistance of Appeals', 'value' => 3, 'color' => '#35a964')
        ));

$pdf = new \Kendo\UI\SchedulerPdf();
$pdf->fileName('Kendo UI Scheduler Export.pdf')
    ->proxyURL('pdf-export.php?type=save');

$scheduler = new \Kendo\UI\Scheduler('scheduler');
$scheduler->timezone("Etc/UTC") // set timezone
     ->addResource($resource) // add resource configuration
     ->pdf($pdf)
     ->addToolbarItem(new \Kendo\UI\SchedulerToolbarItem('pdf'))
     ->addView('day', 'week', 'month', 'agenda') // configure views
     ->dataSource($dataSource);// add dataSource

echo $scheduler->render();

?>

<script>

    $("#people :checkbox").change(function(e) {
        var checked = $.map($("#people :checked"), function(checkbox) {
            return parseInt($(checkbox).val());
        });

        var filter = {
            logic: "or",
            filters: $.map(checked, function(value) {
                return {
                    operator: "eq",
                    field: "ownerId",
                    value: value
                };
            })
        };

        var scheduler = $("#scheduler").data("kendoScheduler");
        scheduler.dataSource.filter(filter);
    });

</script>


</body>

</html>

