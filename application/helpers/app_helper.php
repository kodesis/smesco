<?php

function shipment_status_badge($status)
{
	$map = [
		'BOOKED'               => ['bg-cyan',              'Booked'],
		'READY_TO_PICKUP'      => ['bg-yellow text-dark',  'Ready to Pickup'],
		'PICKED_UP'            => ['bg-teal',              'Picked Up'],
		'RECEIVED_ORIGIN'      => ['bg-indigo',            'Received at Origin'],
		'MANIFESTED'           => ['bg-blue',              'Manifested'],
		'DEPARTED'             => ['bg-orange',            'Departed'],
		'ARRIVED'              => ['bg-purple',            'Arrived'],
		'RECEIVED_DESTINATION' => ['bg-cyan',              'Received at Destination'],
		'DELIVERED'            => ['bg-success',           'Delivered'],
		'CANCELLED'            => ['bg-danger',            'Cancelled'],
	];

	$config = $map[$status] ?? ['bg-secondary', $status];

	return '<span class="badge ' . $config[0] . '">' . $config[1] . '</span>';
}
