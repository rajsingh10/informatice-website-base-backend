<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrustSection extends Model
{
    protected $table = 'trust_sections';

    protected $fillable = [
        'eyebrow',
        'title',
        'description',
        'buttonText',
        'buttonUrl',
        'backgroundImage',
        'clients',
    ];

    protected $casts = [
        'clients' => 'array',
    ];

    /**
     * Default dataset matching the user specification.
     */
    public static function defaults(): array
    {
        return [
            'eyebrow'         => '',
            'title'           => 'Trusted by Industry Leaders',
            'description'     => 'Partnering with forward-thinking companies to deliver exceptional digital solutions',
            'buttonText'      => '',
            'buttonUrl'       => '',
            'backgroundImage' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1600&q=80',
            'clients'         => [
                [
                    'id'        => 'techvista',
                    'name'      => 'TechVista',
                    'logo'      => 'https://placehold.co/150x60/3b82f6/ffffff?text=TechVista',
                    'altText'   => 'TechVista',
                    'url'       => '',
                    'isVisible' => true,
                    'order'     => 1,
                ],
                [
                    'id'        => 'growthhub',
                    'name'      => 'GrowthHub',
                    'logo'      => 'https://placehold.co/150x60/8b5cf6/ffffff?text=GrowthHub',
                    'altText'   => 'GrowthHub',
                    'url'       => '',
                    'isVisible' => true,
                    'order'     => 2,
                ],
                [
                    'id'        => 'startuplane',
                    'name'      => 'StartupLane',
                    'logo'      => 'https://placehold.co/150x60/ec4899/ffffff?text=StartupLane',
                    'altText'   => 'StartupLane',
                    'url'       => '',
                    'isVisible' => true,
                    'order'     => 3,
                ],
                [
                    'id'        => 'retailmax',
                    'name'      => 'RetailMax',
                    'logo'      => 'https://placehold.co/150x60/10b981/ffffff?text=RetailMax',
                    'altText'   => 'RetailMax',
                    'url'       => '',
                    'isVisible' => true,
                    'order'     => 4,
                ],
                [
                    'id'        => 'medlife',
                    'name'      => 'MedLife',
                    'logo'      => 'https://placehold.co/150x60/f59e0b/ffffff?text=MedLife',
                    'altText'   => 'MedLife',
                    'url'       => '',
                    'isVisible' => true,
                    'order'     => 5,
                ],
                [
                    'id'        => 'zenith',
                    'name'      => 'Zenith',
                    'logo'      => 'https://placehold.co/150x60/6366f1/ffffff?text=Zenith',
                    'altText'   => 'Zenith',
                    'url'       => '',
                    'isVisible' => true,
                    'order'     => 6,
                ],
            ],
        ];
    }

    /**
     * Get or initialize the singleton record.
     */
    public static function getSingleton(): static
    {
        $instance = static::first();
        if (! $instance) {
            $instance = static::create(static::defaults());
        }

        return $instance;
    }

    /**
     * Normalize an array of client objects.
     */
    public static function normalizeClients(array $rawClients): array
    {
        $normalized = [];
        $order = 1;

        foreach ($rawClients as $item) {
            if (! is_array($item)) {
                continue;
            }

            $name = (string) ($item['name'] ?? 'Client');
            $id = ! empty($item['id']) ? (string) $item['id'] : Str::slug($name);
            if (empty($id)) {
                $id = 'client-' . $order;
            }

            $normalized[] = [
                'id'        => $id,
                'name'      => $name,
                'logo'      => (string) ($item['logo'] ?? ''),
                'altText'   => (string) ($item['altText'] ?? $item['alt'] ?? $name),
                'url'       => (string) ($item['url'] ?? $item['link'] ?? ''),
                'isVisible' => isset($item['isVisible'])
                    ? (bool) filter_var($item['isVisible'], FILTER_VALIDATE_BOOLEAN)
                    : (isset($item['visible']) ? (bool) filter_var($item['visible'], FILTER_VALIDATE_BOOLEAN) : true),
                'order'     => isset($item['order']) ? (int) $item['order'] : $order,
            ];

            $order++;
        }

        usort($normalized, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        return array_values($normalized);
    }

    /**
     * Format payload for public or admin response.
     */
    public function toPayload(bool $isPublic = false): array
    {
        $clients = is_array($this->clients) ? $this->clients : static::defaults()['clients'];
        $clients = static::normalizeClients($clients);

        if ($isPublic) {
            $clients = array_values(array_filter($clients, fn ($c) => ! empty($c['isVisible'])));
        }

        return [
            'eyebrow'         => (string) ($this->eyebrow ?? ''),
            'title'           => (string) ($this->title ?? 'Trusted by Industry Leaders'),
            'description'     => (string) ($this->description ?? ''),
            'buttonText'      => (string) ($this->buttonText ?? ''),
            'buttonUrl'       => (string) ($this->buttonUrl ?? ''),
            'backgroundImage' => (string) ($this->backgroundImage ?? ''),
            'clients'         => $clients,
        ];
    }

    /**
     * Add a single new client.
     */
    public function addClient(array $clientData): array
    {
        $clients = is_array($this->clients) ? $this->clients : static::defaults()['clients'];
        $clients = static::normalizeClients($clients);

        $name = (string) ($clientData['name'] ?? 'Client ' . (count($clients) + 1));
        $id = ! empty($clientData['id']) ? (string) $clientData['id'] : Str::slug($name);
        if (empty($id) || $this->findClientIndex($clients, $id) !== null) {
            $id = Str::slug($name) . '-' . (count($clients) + 1);
        }

        $newOrder = isset($clientData['order']) ? (int) $clientData['order'] : (count($clients) + 1);

        $newClient = [
            'id'        => $id,
            'name'      => $name,
            'logo'      => (string) ($clientData['logo'] ?? ''),
            'altText'   => (string) ($clientData['altText'] ?? $clientData['alt'] ?? $name),
            'url'       => (string) ($clientData['url'] ?? $clientData['link'] ?? ''),
            'isVisible' => isset($clientData['isVisible'])
                ? (bool) filter_var($clientData['isVisible'], FILTER_VALIDATE_BOOLEAN)
                : true,
            'order'     => $newOrder,
        ];

        $clients[] = $newClient;
        $this->clients = static::normalizeClients($clients);
        $this->save();

        return $newClient;
    }

    /**
     * Update a single client by ID.
     */
    public function updateClient(string $id, array $clientData): ?array
    {
        $clients = is_array($this->clients) ? $this->clients : static::defaults()['clients'];
        $idx = $this->findClientIndex($clients, $id);

        if ($idx === null) {
            return null;
        }

        $current = $clients[$idx];
        $updated = [
            'id'        => (string) ($clientData['id'] ?? $current['id']),
            'name'      => (string) ($clientData['name'] ?? $current['name']),
            'logo'      => (string) ($clientData['logo'] ?? $current['logo']),
            'altText'   => (string) ($clientData['altText'] ?? $clientData['alt'] ?? $current['altText'] ?? $current['name']),
            'url'       => (string) (array_key_exists('url', $clientData) ? $clientData['url'] : ($current['url'] ?? '')),
            'isVisible' => array_key_exists('isVisible', $clientData)
                ? (bool) filter_var($clientData['isVisible'], FILTER_VALIDATE_BOOLEAN)
                : (array_key_exists('visible', $clientData) ? (bool) filter_var($clientData['visible'], FILTER_VALIDATE_BOOLEAN) : (bool) ($current['isVisible'] ?? true)),
            'order'     => isset($clientData['order']) ? (int) $clientData['order'] : ($current['order'] ?? ($idx + 1)),
        ];

        $clients[$idx] = $updated;
        $this->clients = static::normalizeClients($clients);
        $this->save();

        return $updated;
    }

    /**
     * Delete a client by ID.
     */
    public function deleteClient(string $id): bool
    {
        $clients = is_array($this->clients) ? $this->clients : static::defaults()['clients'];
        $idx = $this->findClientIndex($clients, $id);

        if ($idx === null) {
            return false;
        }

        array_splice($clients, $idx, 1);
        $this->clients = static::normalizeClients($clients);
        $this->save();

        return true;
    }

    /**
     * Toggle client isVisible state.
     */
    public function toggleClientVisibility(string $id, ?bool $explicitState = null): ?array
    {
        $clients = is_array($this->clients) ? $this->clients : static::defaults()['clients'];
        $idx = $this->findClientIndex($clients, $id);

        if ($idx === null) {
            return null;
        }

        $current = $clients[$idx];
        $newState = $explicitState !== null ? $explicitState : ! (! empty($current['isVisible']));
        $clients[$idx]['isVisible'] = $newState;

        $this->clients = static::normalizeClients($clients);
        $this->save();

        return $clients[$idx];
    }

    /**
     * Reorder clients by an array of IDs.
     */
    public function reorderClients(array $orderedIds): array
    {
        $clients = is_array($this->clients) ? $this->clients : static::defaults()['clients'];
        $byId = [];
        foreach ($clients as $c) {
            $byId[$c['id']] = $c;
        }

        $reordered = [];
        $order = 1;
        foreach ($orderedIds as $oid) {
            if (isset($byId[$oid])) {
                $item = $byId[$oid];
                $item['order'] = $order++;
                $reordered[] = $item;
                unset($byId[$oid]);
            }
        }

        // Append any remaining items
        foreach ($byId as $remaining) {
            $remaining['order'] = $order++;
            $reordered[] = $remaining;
        }

        $this->clients = $reordered;
        $this->save();

        return $reordered;
    }

    /**
     * Helper to find client index in array.
     */
    protected function findClientIndex(array $clients, string $id): ?int
    {
        foreach ($clients as $i => $c) {
            if (isset($c['id']) && (string) $c['id'] === (string) $id) {
                return $i;
            }
        }

        return null;
    }
}

