<?php

namespace App\Filters;

use Illuminate\Http\Request;

/**
 * Class Filters
 * @package App\Filters
 */
abstract class Filters
{
    protected $request,$builder;
    protected $filters = [];

    /**
     * Filters constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $builder
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilers() as $filter => $value) {
           if(method_exists($this,$filter)){
               $this->$filter($value);
           }
        }
        return $this->builder;
    }


    /**
     * getFilers
     */
    protected function getFilers()
    {

        return $this->request->only($this->filters);
    }

}
