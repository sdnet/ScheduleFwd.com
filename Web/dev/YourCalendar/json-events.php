<?php

	$year = date('Y');
	$month = date('m');

	echo json_encode(array(

		array(
			'id' => 109,
			'title' => "My test event",
			'start' => "2012-08-29 07:00:00",
			'end' => "2012-08-29 19:00:00",
			'allDay' => false,
			'shiftName' => "Shift Name",
			'users' => "Steve Adcock, Alex Ebadirad, James Flanagan" 
			),
			
		array(
			'id' => 110,
			'title' => "My test event 2",
			'start' => "2012-08-29 19:00:00",
			'end' => "2012-08-30 07:00:00",
			'allDay' => false,
			'shiftName' => "Shift Name 2",
			'users' => "George Corliss, Andy Walker, Steve Adcock"
			),	
		array(
			'id' => 111,
			'title' => "My test event",
			'start' => "2012-08-30 07:00:00",
			'end' => "2012-08-30 19:00:00",
			'allDay' => false,
			'shiftName' => "Shift Name",
			'users' => "Steve Adcock, Alex Ebadirad, James Flanagan" 
		),
	
		array(
			'id' => 112,
			'title' => "My test event 2",
			'start' => "2012-08-30 19:00:00",
			'end' => "2012-08-31 07:00:00",
			'allDay' => false,
			'shiftName' => "Shift Name 2",
			'users' => "George Corliss, Andy Walker, Steve Adcock"
			),
		
		array(
			'id' => 113,
			'title' => "My test event 3",
			'start' => "$year-08-31 07:00:00",
			'end' => "$year-08-31 19:00:00",
			'allDay' => false,
			'shiftName' => "Shift Name 3",
			'users' => "Henry Ford"
			),
	
		array(
			'id' => 114,
			'title' => "My test event 3",
			'start' => "$year-08-31 19:00:00",
			'end' => "$year-09-01 07:00:00",
			'allDay' => false,
			'shiftName' => "Shift Name 3",
			'users' => "Henry Ford"
			),
	
		array(
			'id' => 115,
			'title' => "My test event 4",
			'start' => "$year-09-01 10:00:00",
			'end' => "$year-09-01 16:00:00",
			'allDay' => false,
			'shiftName' => "Shift Name 4",
			'users' => "Open"
			),
	
	));

?>
