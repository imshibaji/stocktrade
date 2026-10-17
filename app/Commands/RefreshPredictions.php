<?php

namespace App\Commands;

use App\Libraries\PredictionQueryRunner;
use App\Models\PredictionQueryModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RefreshPredictions extends BaseCommand
{
    protected $group = 'predictions';

    protected $name = 'predictions:refresh';

    protected $description = 'Re-run every saved prediction query and regenerate its stored results.';

    protected $usage = 'predictions:refresh [id ...]';

    protected $arguments = [
        'id' => 'Optional query IDs. When given, only those queries are refreshed.',
    ];

    public function run(array $params)
    {
        $runner = new PredictionQueryRunner();
        $queryModel = new PredictionQueryModel();

        $ids = array_values(array_filter($params, static fn ($p) => ctype_digit((string) $p)));

        $queries = $ids
            ? $queryModel->whereIn('id', array_map('intval', $ids))->findAll()
            : $queryModel->findAll();

        if (empty($queries)) {
            CLI::write('No prediction queries found.', 'yellow');
            return;
        }

        $totalResults = 0;
        $failed = 0;

        foreach ($queries as $query) {
            CLI::write("Running #{$query['id']} {$query['name']} [{$query['method']}] ... ", 'white');

            try {
                $results = $runner->run($query);
                $count = count($results);
                $totalResults += $count;

                $queryModel->update($query['id'], [
                    'status' => 'completed',
                    'last_run_at' => date('Y-m-d H:i:s'),
                ]);

                CLI::write("{$count} results", 'green');
            } catch (\Throwable $e) {
                $failed++;
                CLI::write('FAILED: ' . $e->getMessage(), 'red');
            }
        }

        CLI::newLine();
        CLI::write(sprintf(
            'Done. %d results across %d queries%s.',
            $totalResults,
            count($queries),
            $failed ? " ({$failed} failed)" : ''
        ), 'green');
    }
}
