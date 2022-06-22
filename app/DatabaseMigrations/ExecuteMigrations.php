<?php

namespace App\DatabaseMigrations;

require __DIR__.'/../../vendor/autoload.php';

use App\Application;

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

    public function run()
    {
        $exists = Migration::raw('SHOW TABLES LIKE "migrations";');

        if (empty($exists)) {
            (new Migration())->up();
        }

        foreach ($this->migrations as $migration) {
            /** @var MigrationInterface $migrationInstance */
            $migrationInstance = new $migration();
            $exists = Migration::find(['name' => $migration, 'step' => $migrationInstance->getStep()], 1);

            if (!empty($exists)) {
                continue;
            }

            $migrationInstance->up();

            $storeMigration = new Migration();
            $storeMigration->name = $migration;
            $storeMigration->step = $migrationInstance->getStep();
            $storeMigration->save();

        }

    }
}