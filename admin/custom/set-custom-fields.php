<?php
/**
 * Set custom fields
 */
use Bodybuilder\plugin\admin\custom\Exercise_Custom_Fields;
use Bodybuilder\plugin\admin\custom\Post_Custom_Fields;

$custom_fields_exercise = new Exercise_Custom_Fields();
$custom_fields_post = new Post_Custom_Fields();

$excercise_fields = array(
	array(
		'label' => 'Category',
		'desc'  => 'Add a category that the exercise belongs to. Examples: "Core", "Legs", "Yoga", etc...',
		'id'    => "exercise_category",
		'type'  => 'text'
	),
	array(
		'label' => 'Instructions',
		'desc'  => 'Step by step instructions on how to perform the exercise. Skip to new line for each new instruction. **Do not number**',
		'id'    => 'exercise_instructions',
		'type'  => 'textarea'
	),
	array(
		'label' => 'Caution',
		'desc'  => 'Add a caution about this exercise.',
		'id'    => 'exercise_caution',
		'type'  => 'textarea'
	),
	array(
		'label' => 'Exercise Image',
		'desc'  => 'Add an image of the exercise being performed.',
		'id'    => 'exercise_image',
		'type'  => 'image'
	)
);
$custom_fields_exercise->set_exercise_meta_fields( $excercise_fields );

$workout_fields = array(
	array(
		'label' => 'Workout Name',
		'desc'  => 'Name the workout',
		'id'    => 'workout_name',
		'type'  => 'text'
	),
	array(
		'label' => 'Author',
		'desc'  => 'Who designed this workout?',
		'id'    => 'workout_author',
		'type'  => 'text'
	),
	array(
		'label' => 'Description',
		'desc'  => 'Give a brief overview of the workout (SEO Enhancement)',
		'id'    => 'workout_description',
		'type'  => 'wysiwyg'
	),
	array(
		'label' => 'Intensity',
		'desc'  => 'Average target heartrate (SEO Enhancement)',
		'id'    => 'workout_intensity',
		'type'  => 'text'
	),
	array(
		'label' => 'Rest Periods',
		'desc'  => 'How many rest days are there per week? (SEO Enhancement)',
		'id'    => 'workout_rest_periods',
		'type'  => 'text'
	),
	array(
		'label' => 'Repetitions',
		'desc'  => 'How many weeks should one do this workout? (SEO Enhancement)',
		'id'    => 'workout_repetitions',
		'type'  => 'text'
	),
	array(
		'label' => 'Workload',
		'desc'  => 'What is the energy expenditure? e.g., calories burned (SEO Enhancement)',
		'id'    => 'workout_workload',
		'type'  => 'text'
	),
	array(
		'label' => 'Category',
		'desc'  => 'Add a category that the exercise belongs to. Examples: "Core", "Legs", "Yoga", etc... (SEO Enhancement)',
		'id'    => 'workout_category',
		'type'  => 'text'
	),
	array(
		'label' => 'Average Workout Time',
		'desc'  => 'Average time it takes to complete each workout (SEO Enhancement)',
		'id'    => 'workout_duration',
		'type'  => 'text'
	),
	array(
		'label' => 'Workout Directions',
		'desc'  => 'Add instructions for performing the workout. Skip to new line for each new instruction. **Do not number** (SEO Enhancement)',
		'id'    => 'workout_instructions',
		'type'  => 'textarea'
	),
	array(
		'label' => 'Workout Image',
		'desc'  => 'Add an image of the exercise being performed. (SEO Enhancement)',
		'id'    => 'workout_image',
		'type'  => 'image'
	),
	array(
		'label' => 'Add Day',
		'desc'  => 'Add exercises for a day.',
		'id'    => 'workout_day',
		'type'  => 'day-repeater'
	)
);
$custom_fields_post->set_workout_meta_fields( $workout_fields );
