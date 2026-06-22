<?php


namespace App\Services;

/**Assumptions:
 *  Associated class will reach context builder once it has been vetted to contain records on database.
 *  Context is built on validated models
 * */
interface ContextBuilderService{
    public function constructContext();
}