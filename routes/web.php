<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('borrowers', 'BorrowerController@index');
$app->post('borrowers', 'BorrowerController@create');
$app->get('borrowers/{id}', 'BorrowerController@show');
$app->patch('borrowers/{id}', 'BorrowerController@edit');
$app->delete('borrowers/{id}', 'BorrowerController@destroy');
$app->post('borrowers/{id}/loan-requests/{loanRequestId}/repayments/{repaymentId}/paid', 'BorrowerController@repayment');

$app->get('/loan-requests', 'LoanRequestController@index');
$app->post('/loan-requests', 'LoanRequestController@create');
$app->get('/loan-requests/{id}', 'LoanRequestController@show');
$app->patch('/loan-requests/{id}', 'LoanRequestController@edit');
$app->delete('/loan-requests/{id}', 'LoanRequestController@destroy');
$app->post('/loan-requests/{id}/approved', 'LoanRequestController@approved');
