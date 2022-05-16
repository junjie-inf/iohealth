<?php

return array(

	'subject' => array(
		'report' => array(
			'store' => 'Report :title created',
			'update' => 'Report :title updated',
			'destroy' => 'Report :title deleted',
		),
		'comment' => array(
			'store' => 'New comment in :report.title',
			'destroy' => 'Comment deleted in :report.title',
		),
		'reminder' => array(
			'passwordreset' => 'Password Reset',
			'completeform' => 'To reset your password, complete this form:',
		),
		'destroy' => array(
			'theuser' => 'The user',
			'hasdeleted' => 'has deleted a comment',
		),
		'store' => array(
			'theuser' => 'The user',
			'hascreated' => 'ha published a new comment',
		),
	)

);
