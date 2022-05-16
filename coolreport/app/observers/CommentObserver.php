<?php

class CommentObserver {

    public function created($model)
    {
    	Mail::bulk('comment.store', $model, $model->report->getAssociatedFollowers(true));
    }

    public function deleted($model)
    {
        Mail::bulk('comment.destroy', $model, $model->report->getAssociatedFollowers());
    }

}