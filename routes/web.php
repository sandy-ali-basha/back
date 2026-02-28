<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

require __DIR__.'/auth.php';
Route::get('/reset_password?token={token}', [UserController::class, 'restPasswordView']);

if (config('app.debug')) {
    Route::get('all-routes', function () {
        $routeCollection = Route::getRoutes();
        echo "<head>";
        echo "<style>";
        echo "tr {color: #717171;}";
        echo "tr:hover {color: #000;border-bottom: solid 1px #000;}";
        echo "th {position: sticky;top: 0;background: #fff;}";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<table style='width:100%'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th><h4>#</h4></td>";
        echo "<th><h4>HTTP Method</h4></td>";
        echo "<th><h4>Route</h4></td>";
        if (request()->has('action')) {
            echo "<th><h4>Corresponding Action</h4></td>";
        }
        if (request()->has('view')) {
            echo "<th><h4>View</h4></td>";
        }
        if (request()->has('name')) {
            echo "<th><h4>Name</h4></td>";
        }
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($routeCollection as $k => $value) {
            // if ($k === 527) dd($value->defaults['_config']['view']);
            echo "<tr>";
            echo "<td>" . $k + 1 . "</td>";
            echo "<td>" . $value->methods()[0] . "</td>";
            echo "<td>" . $value->uri() . "</td>";
            if (request()->has('action')) {
                echo "<td>" . $value->getActionName() . "</td>";
            }
            if (request()->has('view')) {
                echo "<td>" . ($value->defaults['_config']['view'] ?? "") . "</td>";
            }
            if (request()->has('name')) {
                echo "<td>" . $value->getName() . "</td>";
            }
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</body>";
    });
}