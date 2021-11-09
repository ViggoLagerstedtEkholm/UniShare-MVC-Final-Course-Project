<?php

namespace App\controllers;

use App\Middleware\Middleware;
use App\Core\Application;

/**
 * Abstract class handling displaying/adding middleware/json responses/calculating
 * filtering offsets for pagination.
 * @abstract
 * @author Viggo Lagestedt Ekholm
 */
abstract class Controller
{
    public string $action = '';
    protected array $middlewares = [];

    /**
     * Sets the middleware for the controller.
     * @param Middleware $middleware
     */
    public function setMiddlewares(Middleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Gets the middleware for the controller.
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Display the view with the required parameters acquired from the controller.
     * @param string $folder
     * @param string $page
     * @param array $params
     * @return string
     */
    protected function display(string $folder, string $page, array $params): string
    {
        return Application::$app->router->renderView($folder, $page, $params);
    }

    /**
     * Calculate the offset for the pagination.
     * @param int $count
     * @param int $page
     * @param int $result_page_count_selected
     * @return array all parameters to query the database with the offsets calculated.
     */
    protected function calculateOffsets(int $count, int $page, int $result_page_count_selected): array
    {
        $values = array();
        $results_per_page = $result_page_count_selected;
        $number_of_pages = ceil($count / $results_per_page);
        $start_page_first_result = ($page - 1) * $results_per_page;

        $values['number_of_pages'] = $number_of_pages;
        $values['results_per_page'] = $results_per_page;
        $values['start_page_first_result'] = $start_page_first_result;
        return $values;
    }

    /**
     * Returns a json response that we can use to handle responses from user requests.
     * @param resp the JSON type data.
     * @param int $code
     * @return false|string
     */
    protected function jsonResponse(mixed $resp, int $code): bool|string
    {
        http_response_code($code);
        return json_encode($resp);
    }
}
