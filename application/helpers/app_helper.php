<?php

function shipment_status_badge($status)
{
	$map = [
		'BOOKED'               => ['bg-cyan',   'Booked'],
		'READY_FOR_HANDOVER'   => ['bg-lime',   'Ready for Handover'],
		'HANDOVER_TO_COURIER'  => ['bg-pink',   'Handed to Courier'],
		'READY_FOR_FORWARDING' => ['bg-azure',  'Ready for Forwarding'],
		'READY_TO_PICKUP'      => ['bg-yellow', 'Ready to Pickup'],
		'PICKED_UP'            => ['bg-teal',   'Picked Up'],
		'RECEIVED_ORIGIN'      => ['bg-indigo', 'Received at Origin'],
		'OFFLOADED'            => ['bg-orange', 'Offloaded'],
		'CONSOLIDATED'         => ['bg-purple', 'Consolidated'],
		'IN_TRANSIT'           => ['bg-azure',  'In Transit'],
		'MANIFESTED'           => ['bg-blue',   'Manifested'],
		'DEPARTED'             => ['bg-orange', 'Departed'],
		'ARRIVED'              => ['bg-purple', 'Arrived'],
		'RECEIVED_DESTINATION' => ['bg-cyan',   'Received at Destination'],
		'DELIVERED'            => ['bg-success', 'Delivered'],
		'CANCELLED'            => ['bg-danger', 'Cancelled'],
	];

	$config = $map[$status] ?? ['bg-secondary', htmlspecialchars((string) $status)];

	return '<span class="badge ' . $config[0] . '">' . $config[1] . '</span>';
}
