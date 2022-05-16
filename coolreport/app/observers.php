<?php

/*
|--------------------------------------------------------------------------
| Observer subscribers
|--------------------------------------------------------------------------
*/

// Report
Report::observe(new ReportObserver);

// Comment
Comment::observe(new CommentObserver);