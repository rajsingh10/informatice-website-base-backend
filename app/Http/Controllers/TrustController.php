<?php

namespace App\Http\Controllers;

use App\Models\TrustSection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrustController extends Controller
{
    /**
     * Helper to decode raw JSON body if request->all() is empty.
     */
    protected function parseJsonInput(Request $request): array
    {
        $input = $request->all();
        if (empty($input) && ! empty($request->getContent())) {
            $decoded = json_decode($request->getContent(), true);
            if (! is_array($decoded)) {
                $clean = mb_convert_encoding($request->getContent(), 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
                $decoded = json_decode($clean, true);
            }
            if (is_array($decoded)) {
                $input = $decoded;
            }
        }

        return $input;
    }

    /**
     * GET /api/public/trust & /api/trust — Public View (No Auth)
     */
    public function showPublic(): JsonResponse
    {
        $section = TrustSection::getSingleton();

        return response()->json([
            'success' => true,
            'data'    => $section->toPayload(true),
        ], 200);
    }

    /**
     * GET /api/admin/trust — Admin View (Auth Required)
     */
    public function showAdmin(): JsonResponse
    {
        $section = TrustSection::getSingleton();

        return response()->json([
            'success' => true,
            'message' => 'Trust section fetched successfully',
            'data'    => $section->toPayload(false),
        ], 200);
    }

    /**
     * PUT|POST /api/admin/trust — Update entire Trust Section (Auth Required)
     */
    public function updateSection(Request $request): JsonResponse
    {
        $input = $this->parseJsonInput($request);

        $validator = Validator::make($input, [
            'eyebrow'         => 'sometimes|nullable|string|max:255',
            'title'           => 'sometimes|nullable|string|max:255',
            'description'     => 'sometimes|nullable|string',
            'buttonText'      => 'sometimes|nullable|string|max:255',
            'buttonUrl'       => 'sometimes|nullable|string|max:1024',
            'backgroundImage' => 'sometimes|nullable|string|max:2048',
            'clients'         => 'sometimes|nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $section = TrustSection::getSingleton();

        $fillable = ['eyebrow', 'title', 'description', 'buttonText', 'buttonUrl', 'backgroundImage'];
        foreach ($fillable as $f) {
            if (array_key_exists($f, $input)) {
                $section->{$f} = (string) ($input[$f] ?? '');
            }
        }

        if (array_key_exists('clients', $input) && is_array($input['clients'])) {
            $section->clients = TrustSection::normalizeClients($input['clients']);
        }

        $section->save();

        return response()->json([
            'success' => true,
            'message' => 'Trust section updated successfully',
            'data'    => $section->toPayload(false),
        ], 200);
    }

    /**
     * GET /api/admin/trust/clients — Get all clients (Auth Required)
     */
    public function getClients(): JsonResponse
    {
        $section = TrustSection::getSingleton();
        $payload = $section->toPayload(false);

        return response()->json([
            'success' => true,
            'message' => 'Clients fetched successfully',
            'total'   => count($payload['clients']),
            'data'    => $payload['clients'],
        ], 200);
    }

    /**
     * POST /api/admin/trust/clients — Add a new client (Auth Required)
     */
    public function storeClient(Request $request): JsonResponse
    {
        $input = $this->parseJsonInput($request);

        $validator = Validator::make($input, [
            'name'      => 'required|string|max:255',
            'logo'      => 'sometimes|nullable|string|max:2048',
            'altText'   => 'sometimes|nullable|string|max:255',
            'url'       => 'sometimes|nullable|string|max:1024',
            'isVisible' => 'sometimes|boolean',
            'order'     => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $section = TrustSection::getSingleton();
        $newClient = $section->addClient($input);

        return response()->json([
            'success' => true,
            'message' => 'Client added successfully',
            'data'    => $newClient,
        ], 201);
    }

    /**
     * GET /api/admin/trust/clients/{id} — View single client (Auth Required)
     */
    public function showClient(string $id): JsonResponse
    {
        $section = TrustSection::getSingleton();
        $payload = $section->toPayload(false);

        foreach ($payload['clients'] as $client) {
            if ((string) $client['id'] === (string) $id) {
                return response()->json([
                    'success' => true,
                    'data'    => $client,
                ], 200);
            }
        }

        return response()->json([
            'success' => false,
            'message' => "Client with ID '{$id}' not found.",
        ], 404);
    }

    /**
     * PUT|PATCH /api/admin/trust/clients/{id} — Update single client (Auth Required)
     */
    public function updateClient(Request $request, string $id): JsonResponse
    {
        $input = $this->parseJsonInput($request);

        $validator = Validator::make($input, [
            'name'      => 'sometimes|nullable|string|max:255',
            'logo'      => 'sometimes|nullable|string|max:2048',
            'altText'   => 'sometimes|nullable|string|max:255',
            'url'       => 'sometimes|nullable|string|max:1024',
            'isVisible' => 'sometimes|boolean',
            'order'     => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $section = TrustSection::getSingleton();
        $updated = $section->updateClient($id, $input);

        if (! $updated) {
            return response()->json([
                'success' => false,
                'message' => "Client with ID '{$id}' not found.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Client updated successfully',
            'data'    => $updated,
        ], 200);
    }

    /**
     * DELETE /api/admin/trust/clients/{id} — Delete single client (Auth Required)
     */
    public function destroyClient(string $id): JsonResponse
    {
        $section = TrustSection::getSingleton();
        $deleted = $section->deleteClient($id);

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => "Client with ID '{$id}' not found.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Client deleted successfully',
        ], 200);
    }

    /**
     * PATCH /api/admin/trust/clients/{id}/visibility — Toggle isVisible (Auth Required)
     */
    public function toggleVisibility(Request $request, string $id): JsonResponse
    {
        $input = $this->parseJsonInput($request);
        $explicit = null;

        if (array_key_exists('isVisible', $input)) {
            $explicit = (bool) filter_var($input['isVisible'], FILTER_VALIDATE_BOOLEAN);
        } elseif (array_key_exists('visible', $input)) {
            $explicit = (bool) filter_var($input['visible'], FILTER_VALIDATE_BOOLEAN);
        }

        $section = TrustSection::getSingleton();
        $updated = $section->toggleClientVisibility($id, $explicit);

        if (! $updated) {
            return response()->json([
                'success' => false,
                'message' => "Client with ID '{$id}' not found.",
            ], 404);
        }

        $statusText = $updated['isVisible'] ? 'visible' : 'hidden';

        return response()->json([
            'success' => true,
            'message' => "Client is now {$statusText}",
            'data'    => $updated,
        ], 200);
    }

    /**
     * PUT|PATCH|POST /api/admin/trust/clients/reorder — Reorder clients (Auth Required)
     */
    public function reorder(Request $request): JsonResponse
    {
        $input = $this->parseJsonInput($request);

        $orderIds = $input['order'] ?? ($input['ids'] ?? ($input['clientIds'] ?? []));

        // Allow passing full array of objects [{id: '...'}, ...]
        if (is_array($orderIds)) {
            $orderIds = array_map(function ($item) {
                return is_array($item) ? ($item['id'] ?? '') : (string) $item;
            }, $orderIds);
        }

        if (empty($orderIds) && is_array($input) && array_is_list($input)) {
            $orderIds = array_map(function ($item) {
                return is_array($item) ? ($item['id'] ?? '') : (string) $item;
            }, $input);
        }

        if (empty($orderIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide an array of client IDs in order.',
            ], 422);
        }

        $section = TrustSection::getSingleton();
        $reordered = $section->reorderClients($orderIds);

        return response()->json([
            'success' => true,
            'message' => 'Clients reordered successfully',
            'data'    => $reordered,
        ], 200);
    }

    /**
     * POST /api/admin/trust/reset — Reset to factory defaults (Auth Required)
     */
    public function reset(): JsonResponse
    {
        $section = TrustSection::getSingleton();
        $section->update(TrustSection::defaults());

        return response()->json([
            'success' => true,
            'message' => 'Trust section reset to defaults successfully',
            'data'    => $section->toPayload(false),
        ], 200);
    }
}

