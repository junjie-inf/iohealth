<?php

/*
|--------------------------------------------------------------------------
| Event subscribers
|--------------------------------------------------------------------------
*/

// Auth
Event::subscribe( new AuthEventHandler );
Event::subscribe( new ReportEventHandler );
Event::subscribe( new CommentEventHandler );
