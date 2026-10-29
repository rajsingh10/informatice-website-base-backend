<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    /**
     * Public list of team members.
     * Supports ?department=, ?isLeadership=, ?limit=.
     * Only returns published team members sorted by order asc, id asc.
     */
    public function indexPublic(Request $request): JsonResponse
    {
        $query = TeamMember::published();

        if ($request->filled('department')) {
            $query->department($request->query('department'));
        }

        if ($request->has('isLeadership')) {
            $query->leadership(filter_var($request->query('isLeadership'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('designation', 'like', $search)
                    ->orWhere('department', 'like', $search)
                    ->orWhere('bio', 'like', $search);
            });
        }

        $total = $query->count();
        $query->orderBy('order', 'asc')->orderBy('id', 'asc');

        if ($request->filled('limit')) {
            $limit = max(1, (int) $request->query('limit', 20));
            $page = max(1, (int) $request->query('page', 1));
            $members = $query->forPage($page, $limit)->get();
        } else {
            $members = $query->get();
        }

        return response()->json([
            'success' => true,
            'total' => $total,
            'data' => $members->map(fn ($member) => $member->toPublicPayload()),
        ], 200);
    }

    /**
     * Public single team member by id.
     */
    public function showPublic(int $id): JsonResponse
    {
        $member = TeamMember::published()->find($id);

        if (! $member) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $member->toPublicPayload(),
        ], 200);
    }

    /**
     * Admin list of all team members.
     * Returns both published and unpublished members.
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        $query = TeamMember::query();

        if ($request->filled('department')) {
            $query->department($request->query('department'));
        }

        if ($request->has('isLeadership')) {
            $query->leadership(filter_var($request->query('isLeadership'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
        }

        if ($request->has('isPublished')) {
            $isPublished = filter_var($request->query('isPublished'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isPublished !== null) {
                $query->where('isPublished', $isPublished);
            }
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('designation', 'like', $search)
                    ->orWhere('department', 'like', $search)
                    ->orWhere('bio', 'like', $search);
            });
        }

        $total = $query->count();
        $query->orderBy('order', 'asc')->orderBy('id', 'asc');

        if ($request->filled('limit')) {
            $limit = max(1, (int) $request->query('limit', 20));
            $page = max(1, (int) $request->query('page', 1));
            $members = $query->forPage($page, $limit)->get();
        } else {
            $members = $query->get();
        }

        return response()->json([
            'success' => true,
            'total' => $total,
            'data' => $members->map(fn ($member) => $member->toAdminPayload()),
        ], 200);
    }

    /**
     * Admin single team member by id.
     */
    public function showAdmin(int $id): JsonResponse
    {
        $member = TeamMember::find($id);

        if (! $member) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $member->toAdminPayload(),
        ], 200);
    }

    /**
     * Admin create a new team member.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), TeamMember::validationRules(false));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (! isset($data['order']) || $data['order'] === null) {
            $maxOrder = TeamMember::max('order');
            $data['order'] = $maxOrder !== null ? $maxOrder + 1 : 1;
        }

        if (! isset($data['isLeadership'])) {
            $data['isLeadership'] = false;
        }

        if (! isset($data['isPublished'])) {
            $data['isPublished'] = true;
        }

        if (empty($data['social']) || ! is_array($data['social'])) {
            $data['social'] = TeamMember::defaultSocial();
        }

        $member = TeamMember::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Team member created successfully.',
            'data' => $member->toAdminPayload(),
        ], 201);
    }

    /**
     * Admin update team member.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $member = TeamMember::find($id);

        if (! $member) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), TeamMember::validationRules(true));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (array_key_exists('social', $data) && is_array($data['social'])) {
            $existing = is_array($member->social) ? $member->social : TeamMember::defaultSocial();
            $data['social'] = array_merge($existing, $data['social']);
        }

        $member->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Team member updated successfully.',
            'data' => $member->fresh()->toAdminPayload(),
        ], 200);
    }

    /**
     * Admin delete team member.
     */
    public function destroy(int $id): JsonResponse
    {
        $member = TeamMember::find($id);

        if (! $member) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found.',
            ], 404);
        }

        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team member deleted successfully.',
        ], 200);
    }
}
