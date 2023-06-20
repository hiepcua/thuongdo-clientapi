<?php


namespace App\Services;


use App\Models\Organization;

class OrganizationService implements Service
{
    public function getOrganizationByDomain(string $domain): string
    {
        return optional(Organization::query()->where('domain', $domain)->first())->id ?? $this->getOrganizationDefault(
            );
    }

    public function getOrganizationDefault(): string
    {
        return $this->getOrganizationByDomain('*');
    }
}