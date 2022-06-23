<?php

namespace App\DatabaseMigrations;

require __DIR__.'/../../vendor/autoload.php';

class ExecuteMigrations
{
    protected $migrations = [
        CreateTenantsTable::class,
        CreatePropertyOwnersTable::class,
        CreatePropertiesTable::class,
        CreatePropertyContractsTable::class,
        CreateContractPaymentsTable::class,
        CreateContractPaymentTransfersTable::class
    ];

    public function run($up = true)
    {
        $exists = Migration::raw('SHOW TABLES LIKE "migrations";');

        if (empty($exists)) {
            (new Migration())->up();
        }

        $executed = [];
        $migrations = $up ? $this->migrations : array_reverse($this->migrations);

        foreach ($migrations as $migration) {
            /** @var MigrationInterface $migrationInstance */
            $migrationInstance = new $migration();
            $exists = Migration::find(['name' => $migration, 'step' => $migrationInstance->getStep()], 1);

            if ($up && !empty($exists)) {
                continue;
            }

            echo ' Running migration ' . $migration . PHP_EOL;

            if ($up) {
                $migrationInstance->up();

                $storeMigration = new Migration();
                $storeMigration->name = $migration;
                $storeMigration->step = $migrationInstance->getStep();
                $storeMigration->save();

            } else if (!empty($exists)) {
                $migrationInstance->down();
                $exists->delete();
            } else {
                continue;
            }

            $executed [] = $migration;
            echo ' Done migration '  . $migration . PHP_EOL;


        }

        if (empty($executed)) {
            echo ' Nothing to migrate ' . PHP_EOL;
        }

        echo ' Done ' . PHP_EOL;

    }
}