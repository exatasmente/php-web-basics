<?php

namespace App\Services;

use App\Models\ContractPayment;
use App\Models\ContractPaymentTransfer;
use App\Models\Property;
use App\Models\PropertyContract;
use App\Models\Tenant;

class PropertyContractService
{
    public function createPropertyContract(Property $property, Tenant $tenant, array $propertyContractData)
    {
        $propertyContract = new PropertyContract();
        /** @var PropertyContract $propertyContract */
        $propertyContract = $propertyContract->morph(array_merge($propertyContractData, [
            'property_id' => $property->getId(),
            'tenant_id' => $tenant->getId(),
        ]));

        $propertyContract->save();

        $this->generateContractPayments($propertyContract);

        return $propertyContract;

    }

    public function generateContractPayments(PropertyContract $propertyContract)
    {
        $starts_at = \DateTime::createFromFormat('Y-m-d', $propertyContract->starts_at);
        $ends_at = \DateTime::createFromFormat('Y-m-d', $propertyContract->ends_at);
        $period = PeriodService::make($starts_at, $ends_at);
        $firstPaymentAmount = $period->calculateFirstPaymentAmount($propertyContract->rent_amount);
        $lastPaymentAmount = $period->calculateLastPaymentAmount($propertyContract->rent_amount);

        $numberOfCycles = $period->getNumberOfCycles();
        $this->createContractPayment($propertyContract, [
            'cycle' => 1,
            'starts_at' => $period->getStartDateForCycle(1),
            'ends_at' => $period->getEndDateForCycle(1),
            'amount' => $firstPaymentAmount + $propertyContract->getExtrasTotalAmount(),
            'note' => 'created',
        ]);

        for ($cycle = 2; $cycle <= $numberOfCycles - 1; $cycle += 1) {
            $this->createContractPayment($propertyContract, [
                'cycle' => $cycle,
                'starts_at' => $period->getStartDateForCycle($cycle),
                'ends_at' => $period->getEndDateForCycle($cycle),
                'amount' => $propertyContract->getRentTotalAmount(),
                'note' => 'created',
            ]);
        }

        $this->createContractPayment($propertyContract, [
            'cycle' => $numberOfCycles,
            'starts_at' => $period->getStartDateForCycle($numberOfCycles),
            'ends_at' => $period->getEndDateForCycle($numberOfCycles),
            'amount' => $lastPaymentAmount + $propertyContract->getExtrasTotalAmount(),
            'note' => 'created',
        ]);
    }

    public function createContractPayment(PropertyContract $propertyContract, array $contractPaymentData)
    {
        $contractPayment = new ContractPayment();
        /** @var ContractPayment $contractPayment */
        $contractPayment = $contractPayment->morph(array_merge($contractPaymentData, [
            'property_contract_id' => $propertyContract->getId(),
            'status' => ContractPayment::STATUS_PENDING,
        ]));

        $contractPayment->save();
        $this->createContractPaymentTransfer($contractPayment, $contractPaymentData);

        return $contractPayment;
    }

    public function createContractPaymentTransfer(ContractPayment $contractPayment, array $contractPaymentTransferData)
    {
        $contractPaymentTransfer = new ContractPaymentTransfer();
        /** @var ContractPaymentTransfer $contractPaymentTransfer */
        $contractPaymentTransfer = $contractPaymentTransfer->morph(array_merge($contractPaymentTransferData, [
            'contract_payment_id' => $contractPayment->getId(),
            'status' => ContractPaymentTransfer::STATUS_PENDING,
            'amount' => $contractPayment->getTransferAmount(),
        ]));

        $contractPaymentTransfer->save();

        return $contractPaymentTransfer;
    }

    public function updatePropertyContract(PropertyContract $propertyContract, array $propertyContractData)
    {

    }
}