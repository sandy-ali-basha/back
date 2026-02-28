<?php


namespace App\Http\Patterns;


interface IOperations
{
    public function doOperation(array $data);

    public function audit();

    public function getErrorMessage();

    public function returnPage();

}
