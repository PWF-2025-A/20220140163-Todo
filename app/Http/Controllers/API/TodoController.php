<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Dedoc\Scramble\Support\Generator\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TodoController extends Controller
{
    /**
     * Membuat todo baru.
     *
     * @response 201 {
     *     "status_code": 201,
     *     "message": "Todo berhasil dibuat",
     *     "data": {
     *         "id": 1,
     *         "title": "Contoh Todo",
     *         "user_id": 1,
     *         "category_id": 1,
     *         "is_done": false
     *     }
     * }
     *
     * @response 422 {
     *     "status_code": 422,
     *     "message": "Validasi gagal",
     *     "errors": {
     *         "title": [
     *             "Kolom judul wajib diisi."
     *         ]
     *     }
     * }
     *
     * @response 401 {
     *     "success": false,
     *     "message": "Tidak terautentikasi atau token tidak valid"
     * }
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'nullable|exists:categories,id,user_id,'.Auth::id(),
                'is_done' => 'boolean',
            ]);

            $todo = Todo::create([
                'title' => $data['title'],
                'user_id' => Auth::id(),
                'category_id' => $data['category_id'] ?? null,
                'is_done' => $data['is_done'] ?? false,
            ]);

            return response()->json([
                'status_code' => 201,
                'message' => 'Todo berhasil dibuat',
                'data' => $todo,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Mengambil semua todo milik pengguna yang terautentikasi.
     *
     * @response 200 {
     *     "status_code": 200,
     *     "message": "Todo berhasil diambil",
     *     "data": [
     *         {
     *             "id": 1,
     *             "title": "Contoh Todo",
     *             "user_id": 1,
     *             "category_id": 1,
     *             "is_done": false,
     *             "category": {
     *                 "id": 1,
     *                 "title": "Contoh Kategori",
     *                 "user_id": 1
     *             }
     *         }
     *     ]
     * }
     *
     * @response 401 {
     *     "success": false,
     *     "message": "Tidak terautentikasi atau token tidak valid"
     * }
     */
    public function index()
    {
        $todos = Todo::where('user_id', Auth::id())->with('category')->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'Todo berhasil diambil',
            'data' => $todos,
        ], 200);
    }

    /**
     * Mengambil todo berdasarkan ID milik pengguna yang terautentikasi.
     *
     * @urlParam todo required The ID of the todo.
     *
     * @response 200 {
     *     "status_code": 200,
     *     "message": "Todo berhasil diambil",
     *     "data": {
     *         "id": 1,
     *         "title": "Contoh Todo",
     *         "user_id": 1,
     *         "category_id": 1,
     *         "is_done": false,
     *         "category": {
     *             "id": 1,
     *             "title": "Contoh Kategori",
     *             "user_id": 1
     *         }
     *     }
     * }
     *
     * @response 404 {
     *     "status_code": 404,
     *     "message": "Todo tidak ditemukan",
     *     "data": null
     * }
     *
     * @response 401 {
     *     "success": false,
     *     "message": "Tidak terautentikasi atau token tidak valid"
     * }
     */
    public function show($id)
    {
        $todo = Todo::where('user_id', Auth::id())->with('category')->find($id);

        if (!$todo) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Todo tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Todo berhasil diambil',
            'data' => $todo,
        ], 200);
    }

    /**
     * Memperbarui todo yang ada.
     *
     * @response 200 {
     *     "status_code": 200,
     *     "message": "Todo Berhasil diperbarui",
     *     "data": {
     *         "id": 1,
     *         "title": "Todo Diperbarui",
     *         "user_id": 1,
     *         "category_id": 1,
     *         "is_done": true
     *     }
     * }
     *
     * @response 404 {
     *     "status_code": 404,
     *     "message": "Todo tidak ditemukan"
     * }
     *
     * @response 422 {
     *     "status_code": 422,
     *     "message": "Validasi gagal",
     *     "errors": {
     *         "title": [
     *             "Kolom judul wajib diisi."
     *         ]
     *     }
     * }
     *
     * @response 401 {
     *     "success": false,
     *     "message": "Tidak terautentikasi atau token tidak valid"
     * }
     */
    public function update(Request $request, $id)
    {
        try {
            $todo = Todo::where('user_id', Auth::id())->findOrFail($id);
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'category_id' => 'nullable|exists:categories,id,user_id,' . Auth::id(), // Validasi category milik user
                'is_done' => 'boolean',
            ]);

            $todo->update([
                'title' => $data['title'],
                'category_id' => $data['category_id'] ?? $todo->category_id,
                'is_done' => $data['is_done'] ?? $todo->is_done,
            ]);

            return response()->json([
                'status_code' => 200,
                'message' => 'Todo berhasil diperbarui',
                'data' => $todo->fresh(),
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Todo tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Menghapus todo.
     *
     * @response 200 {
     *     "status_code": 200,
     *     "message": "Todo berhasil dihapus"
     * }
     *
     * @response 404 {
     *     "status_code": 404,
     *     "message": "Todo tidak ditemukan"
     * }
     *
     * @response 401 {
     *     "success": false,
     *     "message": "Tidak terautentikasi atau token tidak valid"
     * }
     */
    public function destroy($id)
    {
        try {
            $todo = Todo::where('user_id', Auth::id())->findOrFail($id);
            $todo->delete();

            return response()->json([
                'status_code' => 200,
                'message' => 'Todo berhasil dihapus',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Todo tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Menandai todo sebagai selesai milik pengguna yang terautentikasi.
     *
     * @urlParam todo required The ID of the todo.
     *
     * @response 200 {
     *     "status_code": 200,
     *     "message": "Todo berhasil diperbarui",
     *     "data": {
     *         "id": 1,
     *         "title": "Contoh Todo",
     *         "user_id": 1,
     *         "category_id": 1,
     *         "is_done": true
     *     }
     * }
     *
     * @response 404 {
     *     "status_code": 404,
     *     "message": "Todo tidak ditemukan",
     *     "data": null
     * }
     *
     * @response 401 {
     *     "success": false,
     *     "message": "Tidak terautentikasi atau token tidak valid"
     * }
     */
    public function markAsDone($id)
    {
        $todo = Todo::where('user_id', Auth::id())->find($id);

        if (!$todo) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Todo tidak ditemukan',
                'data' => null
            ], 404);
        }

        $todo->update(['is_done' => true]);

        return response()->json([
            'status_code' => 200,
            'message' => 'Todo berhasil diperbarui',
            'data' => $todo,
        ], 200);
    }

    /**
     * Menandai todo sebagai belum selesai milik pengguna yang terautentikasi.
     *
     * @urlParam todo required The ID of the todo.
     *
     * @response 200 {
     *     "status_code": 200,
     *     "message": "Todo berhasil diperbarui",
     *     "data": {
     *         "id": 1,
     *         "title": "Contoh Todo",
     *         "user_id": 1,
     *         "category_id": 1,
     *         "is_done": false
     *     }
     * }
     *
     * @response 404 {
     *     "status_code": 404,
     *     "message": "Todo tidak ditemukan",
     *     "data": null
     * }
     *
     * @response 401 {
     *     "success": false,
     *     "message": "Tidak terautentikasi atau token tidak valid"
     * }
     */
    public function markAsUndone($id)
    {
        $todo = Todo::where('user_id', Auth::id())->find($id);

        if (!$todo) {
            return response()->json([
                'status_code' => 404,
                'message' => 'Todo tidak ditemukan',
                'data' => null
            ], 404);
        }

        $todo->update(['is_done' => false]);

        return response()->json([
            'status_code' => 200,
            'message' => 'Todo berhasil diperbarui',
            'data' => $todo,
        ], 200);
    }

    /**
     * Mencari todo berdasarkan judul atau kategori.
     *
     * @response 200 {
     *     "status_code": 200,
     *     "message": "Todo berhasil diambil",
     *     "data": [
     *         {
     *             "id": 1,
     *             "title": "Contoh Todo",
     *             "user_id": 1,
     *             "category_id": 1,
     *             "is_done": false,
     *             "category": {
     *                 "id": 1,
     *                 "title": "Contoh Kategori",
     *                 "user_id": 1
     *             }
     *         }
     *     ]
     * }
     *
     * @response 401 {
     *     "success": false,
     *     "message": "Tidak terautentikasi atau token tidak valid"
     * }
     */
    public function search(Request $request)
    {
        $query = $request->query('q');
        $todos = Todo::where('user_id', Auth::id())
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhereHas('category', function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")->where('user_id', Auth::id());
                    });
            })
            ->with('category')
            ->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'Todo berhasil diambil',
            'data' => $todos,
        ], 200);
    }
}
