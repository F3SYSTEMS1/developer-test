<?php

declare(strict_types=1);

namespace App\Services;

interface ExportServiceInterface
{
    /**
     * @param array $data
     *
     * @return callable
     */
    public function getStream(array $data): callable;
}
