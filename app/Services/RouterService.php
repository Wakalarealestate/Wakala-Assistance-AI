<?php

namespace App\Services;

use App\Handlers\CustomerInquiryHandler;
use App\Handlers\LeadQualificationHandler;
use App\Handlers\ProjectInquiryHandler;

class RouterService{
    public $routes;

    public function __construct(){
        $this->routes = [
            'project_inquiry' => ProjectInquiryHandler::class,
            'customer_service' => CustomerInquiryHandler::class,
            'lead_qualification' => LeadQualificationHandler::class
        ];
    }


    public function resolve($intent){
        return $this->routes[$intent] ?? null;
    }

}