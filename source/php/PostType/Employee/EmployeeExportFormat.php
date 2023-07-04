<?php

namespace VolunteerManager\PostType\Employee;

class EmployeeExportFormat
{
    public const CSV = 'csv';

    public static function isValidFormat($format): bool
    {
        return in_array($format, [self::CSV], true);
    }
}
