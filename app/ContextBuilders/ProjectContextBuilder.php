<?php

namespace App\ContextBuilders;

use App\Models\Properties;
use App\Services\ContextBuilderService;
use Exception;
use Illuminate\Support\Facades\Log;

class ProjectContextBuilder implements ContextBuilderService{
    protected $model;

    public function __construct(){
        $this->model = Properties::class;
    }

    public function constructContext(){
        if(!$this->model){
            throw new Exception("Missing Model");
        }

        try {
            $context = null;
            $results = $this->model::query()->select([
                'name',
                'location',
                'size',
                'metrics',
                'market_price',
                'available_plots',
                'utilities'
            ])
            ->get()
            ->toJson();

            Log::info("Context successfuly built for model ".$this->model.".");
            return $results;
        } catch (\Throwable $th) {
            Log::error("Error in building context: ".$th->getMessage());
            throw new Exception($th->getMessage());
        }
    }
}