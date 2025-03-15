<?php

        namespace App\Http\Controllers;

        use App\Http\Resources\UserResource;
        use App\Models\User;
        use Illuminate\Http\Request;
        use Illuminate\Routing\Controllers\HasMiddleware;
        use Illuminate\Support\Facades\Hash;
        use Lang;

        class UsersController extends Controller implements HasMiddleware
        {
            public static function middleware()
            {
                return ['auth:admin'];
            }

            public function getAll(Request $request)
            {
                $request->validate([
                    'per_page' => 'sometimes|integer|min:1|max:100',
                    'page' => 'nullable|integer|min:1',
                    'sort_by' => 'nullable|string|in:id,name,email,created_at,updated_at',
                    'sort_direction' => 'nullable|string|in:asc,desc',
                    'search' => 'string|max:100|nullable',
                ]);

                $query = User::query()->with(['media']);

                // Search functionality
                if ($request->has('search')) {
                    $searchTerm = "%{$request->search}%";
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('name', 'like', $searchTerm)
                            ->orWhere('email', 'like', $searchTerm);
                    });
                }

                // Sorting
                $sortBy = $request->get('sort_by', 'id');
                $sortDirection = $request->get('sort_direction', 'desc');
                $query->orderBy($sortBy, $sortDirection);

                // Pagination
                $perPage = $request->get('per_page', 10);
                $users = $query->paginate($perPage);

                return $this->ApiResponseFormatted(
                    200,
                    [
                        'data' => UserResource::collection($users),
                        'meta' => [
                            'current_page' => $users->currentPage(),
                            'last_page' => $users->lastPage(),
                            'per_page' => $users->perPage(),
                            'total' => $users->total(),
                            'filters' => $request->only(['search', 'sort_by', 'sort_direction']),
                        ]
                    ],
                    Lang::get('api.success'),
                    $request
                );
            }

            public function getOne(Request $request, $id)
            {
                try {
                    $user = User::with(['media' => function ($query) {
                        $query->where('collection_name', 'avatars');
                    }])->findOrFail($id);

                    return $this->ApiResponseFormatted(
                        200,
                        new UserResource($user),
                        Lang::get('api.success'),
                        $request
                    );
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    return $this->ApiResponseFormatted(
                        404,
                        null,
                        Lang::get('api.not_found'),
                        $request
                    );
                } catch (\Exception $e) {
                    return $this->ApiResponseFormatted(
                        500,
                        null,
                        Lang::get('api.error'),
                        $request
                    );
                }
            }

            public function create(Request $request)
            {
                $validated = $request->validate([
                    'fname' => 'required|string|max:255',
                    'lname' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);

                try {
                    $user = User::create([
                        'name' => $validated['fname'] . ' ' . $validated['lname'],
                        'fname' => $validated['fname'],
                        'lname' => $validated['lname'],
                        'email' => $validated['email'],
                        'password' => Hash::make($validated['password']),
                    ]);

                    // Handle avatar upload
                    if ($request->hasFile('avatar')) {
                        $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
                    }

                    return $this->ApiResponseFormatted(
                        201,
                        new UserResource($user),
                        Lang::get('api.created'),
                        $request
                    );
                } catch (\Exception $e) {
                    return $this->ApiResponseFormatted(
                        500,
                        null,
                        Lang::get('api.error'),
                        $request
                    );
                }
            }

            public function update(Request $request, $id)
            {
                $user = User::find($id);
                if (!$user) {
                    return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
                }

                $validated = $request->validate([
                    'fname' => 'required|string|max:255',
                    'lname' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                    'password' => 'nullable|string|min:8',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'remove_avatar' => 'nullable|in:0,1',
                ]);

                try {
                    // Update user data
                    $updateData = [
                        'name' => $validated['fname'] . ' ' . $validated['lname'],
                        'fname' => $validated['fname'],
                        'lname' => $validated['lname'],
                        'email' => $validated['email'],
                    ];

                    // Update password if provided
                    if (!empty($validated['password'])) {
                        $updateData['password'] = Hash::make($validated['password']);
                    }

                    $user->update($updateData);

                    // Handle avatar upload
                    if ($request->hasFile('avatar')) {
                        $user->clearMediaCollection('avatars');
                        $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
                    } elseif ($request->input('remove_avatar') == '1' && $user->hasMedia('avatars')) {
                        $user->clearMediaCollection('avatars');
                    }

                    return $this->ApiResponseFormatted(
                        200,
                        new UserResource($user),
                        Lang::get('api.updated'),
                        $request
                    );
                } catch (\Exception $e) {
                    return $this->ApiResponseFormatted(
                        500,
                        null,
                        Lang::get('api.error'),
                        $request
                    );
                }
            }

            public function delete(Request $request, $id)
            {
                $user = User::find($id);
                if (!$user) {
                    return $this->ApiResponseFormatted(404, null, Lang::get('api.not_found'), $request);
                }

                $user->delete();
                return $this->ApiResponseFormatted(204, null, Lang::get('api.deleted'), $request);
            }
        }
