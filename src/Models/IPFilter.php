<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Enums\Continent;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Interfaces\Traits\ManagedModelInterface;
use Constellix\Client\Managers\IPFilterManager;
use Constellix\Client\Models\Helpers\IPFilterRegion;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents an IP Filter resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property int $rulesLimit
 * @property Continent[] $continents
 * @property string[] $countries
 * @property int[] $asn;
 * @property string[] $ipv4;
 * @property string[] $ipv6;
 * @property IPFilterRegion[] $regions
 */
class IPFilter extends AbstractModel implements EditableModelInterface, ManagedModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected IPFilterManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
        'rulesLimit' => 100,
        'continents' => [],
        'countries' => [],
        'asn' => [],
        'ipv4' => [],
        'ipv6' => [],
        'regions' => [],
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'name',
        'rulesLimit',
        'continents',
        'countries',
        'asn',
        'ipv4',
        'ipv6',
        'regions',
    ];

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        $payload->continents = array_map(function ($continent) {
            return $continent->value;
        }, $this->continents);
        $payload->regions = array_map(function (IPFilterRegion $region) {
            return $region->transformForApi();
        }, $this->regions);
        return $payload;
    }

    protected function parseApiData(object $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'continents')) {
            $this->props['continents'] = array_map(
                function ($continent) {
                    return Continent::from($continent);
                },
                $data->continents
            );
        }
        if (property_exists($data, 'regions') && is_array($data->regions)) {
            $this->props['regions'] = array_map(function ($data) {
                return new IPFilterRegion($data);
            }, $data->regions);
        }
    }

    protected function addValue(string $property, mixed $value): self
    {
        if (!in_array($value, $this->{$property})) {
            $list = $this->{$property};
            $list[] = $value;
            $this->{$property} = $list;
        }
        return $this;
    }

    public function removeValue(string $property, mixed $value): self
    {
        $index = array_search($value, $this->{$property});
        if ($index !== false) {
            $list = $this->{$property};
            unset($list[$index]);
            $this->{$property} = array_values($list);
        }
        return $this;
    }

    public function addContinent(Continent $continent): self
    {
        return $this->addValue('continents', $continent);
    }

    public function removeContinent(Continent $continent): self
    {
        return $this->removeValue('continents', $continent);
    }

    public function addCountry(string $country): self
    {
        return $this->addValue('countries', $country);
    }

    public function removeCountry(string $country): self
    {
        return $this->removeValue('countries', $country);
    }

    public function addASN(int $asn): self
    {
        return $this->addValue('asn', $asn);
    }

    public function removeASN(int $asn): self
    {
        return $this->removeValue('asn', $asn);
    }

    public function addIPv4(string $ip): self
    {
        return $this->addValue('ipv4', $ip);
    }

    public function removeIPv4(string $ip): self
    {
        return $this->removeValue('ipv4', $ip);
    }

    public function addIPv6(string $ip): self
    {
        return $this->addValue('ipv6', $ip);
    }

    public function removeIPv6(string $ip): self
    {
        return $this->removeValue('ipv6', $ip);
    }

    public function addRegion(IPFilterRegion $region): self
    {
        return $this->addValue('regions', $region);
    }

    public function removeRegion(IPFilterRegion $region): self
    {
        return $this->removeValue('regions', $region);
    }
}
