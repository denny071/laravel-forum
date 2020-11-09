<?php

namespace App\Models\Traits;

use App\Models\Activity;

/**
 * RecordsActivity trait
 */
trait RecordsActivity
{

    /**
     * bootRecordsActivity
     *
     * @return void
     */
    protected static function bootRecordsActivity()
    {
        if(auth()->guest()) return ;

        foreach(static::getActivitiesToRecord() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        static::deleting(function ($model) {
            $model->activity()->delete();
        });
    }

    /**
     * recordActivity
     *
     * @param  mixed $event
     * @return void
     */
    protected function recordActivity($event)
    {
        Activity::create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
            'subject_id' => $this->id,
            'subject_type' => get_class($this)
        ]);
    }


    /**
     * activity
     *
     * @return void
     */
    protected function activity()
    {
        return $this->morphMany('App\Models\Activity','subject');
    }

    /**
     * getActivityType
     *
     * @param  mixed $event
     * @return void
     */
    protected function getActivityType($event)
    {
        $type = strtolower((new \ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }

    /**
     * getActivitiesToRecord
     *
     * @return array
     */
    protected static function getActivitiesToRecord()
    {
        return ['created'];
    }
}
