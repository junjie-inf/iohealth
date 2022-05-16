<?php

class ReportObserver {

    public function created($model)
    {
        //Mail::bulk('report.store', $model, $model->getAssociatedFollowers());
    }

    public function updated($model)
    {
       // Mail::bulk('report.update', $model, $model->getAssociatedFollowers());
    }

    public function deleted($model)
    {
        //Mail::bulk('report.destroy', $model, $model->getAssociatedFollowers());
    }

}