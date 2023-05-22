<?php

namespace VolunteerManager\API;

use WP_Error;

class FormatRequest implements RestFormatInterface
{
    /**
     * @inheritDoc
     */
    public function formatRestRequest(array $request)
    {
        return $request;
    }
}
