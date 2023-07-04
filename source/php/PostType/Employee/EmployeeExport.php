<?php

namespace VolunteerManager\PostType\Employee;

use Exception;
use WP_Query;

class EmployeeExport
{
    private static string $EXPORT_QUERY_PARAM = 'export_users';

    /**
     * @throws Exception
     */
    public function createExportButton(string $exportFormat)
    {
        if (!EmployeeExportFormat::isValidFormat($exportFormat)) {
            throw new Exception('Invalid export format: $exportType');
        }

        $this->renderExportButton($exportFormat);
    }

    public function handleEmployeeExport()
    {
        $exportType = $_GET[self::$EXPORT_QUERY_PARAM] ?? 'NA';

        switch ($exportType) {
            case EmployeeExportFormat::CSV:
                $employees = $this->queryEmployees();
                $this->exportAsCsv($employees);
                break;
            default:
                break;
        }
    }

    private function renderExportButton(string $exportFormat)
    {
        $exportCSVButtonHref = add_query_arg([
            self::$EXPORT_QUERY_PARAM => $exportFormat
        ]);

        echo '<a target="_blank" href="' . $exportCSVButtonHref . '" class="button">' . __('Export as CSV', AVM_TEXT_DOMAIN) . '</a>';
    }

    private function queryEmployees(): array
    {
        $args = $this->buildQueryArgs();

        $filtered_users = new WP_Query($args);

        return $this->processQueryResults($filtered_users);
    }

    private function buildQueryArgs(): array
    {
        // Define the expected parameters.
        $expectedParameters = [
            'm' => ['type' => 'date', 'sanitizer' => 'sanitize_text_field'],
            'swedish_language_proficiency' => ['key' => 'swedish_language_proficiency', 'type' => 'meta', 'sanitizer' => 'sanitize_text_field'],
            'crime_record_extracted' => ['key' => 'crime_record_extracted', 'type' => 'meta', 'sanitizer' => 'intval'],
            'employee-registration-status' => ['taxonomy' => 'employee-registration-status', 'type' => 'tax', 'sanitizer' => 'sanitize_key']
        ];

        // Define query arguments.
        $args = [
            'post_type' => 'employee',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'meta_query' => []
        ];

        // Loop through each expected parameter and build the query.
        foreach ($expectedParameters as $param => $config) {
            $value = $_GET[$param] ?? null;
            if (!$value) {
                continue;
            }

            if (!empty($config['sanitizer']) && function_exists($config['sanitizer'])) {
                $value = call_user_func($config['sanitizer'], $value);
            }

            switch ($config['type']) {
                case 'date':
                    $args['date_query'] = [
                        [
                            'year' => substr($value, 0, 4),
                            'month' => substr($value, 4, 2),
                        ]
                    ];
                    break;
                case 'meta':
                    $args['meta_query'][] = [
                        'key' => $config['key'],
                        'value' => $value,
                        'compare' => '='
                    ];
                    break;
                case 'tax':
                    $args['tax_query'] = [
                        [
                            'taxonomy' => $config['taxonomy'],
                            'field' => 'term_id',
                            'terms' => $value
                        ]
                    ];
                    break;
            }
        }

        return $args;
    }

    private function processQueryResults(WP_Query $employees): array
    {
        $employeeData = [];

        if ($employees->have_posts()) {
            while ($employees->have_posts()) {
                $employees->the_post();
                $postId = get_the_ID();
                $postMeta = get_post_custom($postId);

                $registrationStatusTerms = wp_get_post_terms($postId, 'employee-registration-status', ["fields" => "names"]);
                $registrationStatus = (!empty($registrationStatusTerms)) ? $registrationStatusTerms[0] : '';

                $employeeData[] = [
                    'ID' => $postId,
                    'First name' => $postMeta['first_name'][0] ?? '',
                    'Surname' => $postMeta['surname'][0] ?? '',
                    'National identity number' => $postMeta['national_identity_number'][0] ?? '',
                    'User email' => $postMeta['email'][0] ?? '',
                    'User phone number' => $postMeta['phone_number'][0] ?? '',
                    'Registration status' => $registrationStatus,
                    'Source' => $postMeta['source'][0] ?? '',
                    'Language proficiency' => $postMeta['swedish_language_proficiency'][0] ?? '',
                ];
            }
        }

        return $employeeData;
    }

    private function exportAsCsv(array $employees)
    {
        // Output buffer for CSV in-memory.
        ob_start();
        $file = fopen('php://output', 'w');

        // Add header row.
        if (!empty($employees)) {
            fputcsv($file, array_keys($employees[0]));
        }

        foreach ($employees as $user) {
            fputcsv($file, $user);
        }

        // Get CSV from output buffer.
        $csv = ob_get_clean();

        $fileName = 'exported_volunteers_' . date('Y-m-d') . '.csv';

        // Prompt user to download CSV file.
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($csv));

        // Output CSV.
        echo $csv;

        // Prevent other output.
        exit;
    }
}
