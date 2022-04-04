<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdministrateurController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/admins/list",
     *     tags = {"ADMINISTRATION_ADMINISTRATEUR"},
     *     summary="Liste des administrateurs",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        return User::all();
    }

    /**
     * @OA\Post(
     *     path="/admin/admins/store",
     *     tags = {"ADMINISTRATION_ADMINISTRATEUR"},
     *     summary="Ajouter un administrateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "name",
     *                   type="string",
     *                   example = "Enock"
     *                  ),
     *                @OA\Property(
     *                   property = "email",
     *                   type="string",
     *                   example = "bgi@gmail.com"
     *                  ),
     *                @OA\Property(
     *                   property = "password",
     *                   type="string",
     *                   example = "12345678"
     *                  ),
     *                @OA\Property(
     *                   property = "password_confirmation",
     *                   type="string",
     *                   example = "12345678"
     *                  ),
     *              
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required"
        ]);

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_admin = 1;
        $user->password = Hash::make($request->password);
        $user->save();

        return response([
            "message" => "Administrator added",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/admins/show/{id}",
     *     tags = {"ADMINISTRATION_ADMINISTRATEUR"},
     *     summary="Renvoie les informations d'un administrateur par son ID",
     *      @OA\Parameter(
     *          name = "id",
     *          required = true,
     *          in = "path",
     *          example = 18,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function show($id)
    {
        $user = User::where('id', $id)->where("is_admin", 1)->first();

        if (!$user) {
            return response([
                "message" => "Administrator not found",
                "visibility" => false
            ], 404);
        }

        return $user;
    }

    /**
     * @OA\Post(
     *     path="/admin/admins/update",
     *     tags = {"ADMINISTRATION_ADMINISTRATEUR"},
     *     summary="Editer les informations d'un administrateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "id",
     *                   type="integer",
     *                   example = 10
     *                  ),
     *                @OA\Property(
     *                   property = "name",
     *                   type="string",
     *                   example = "Enock"
     *                  ),
     *                @OA\Property(
     *                   property = "email",
     *                   type="string",
     *                   example = "bgi@gmail.com"
     *                  ),
     *                @OA\Property(
     *                   property = "password",
     *                   type="string",
     *                   example = "12345678"
     *                  )
     *              
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function update(Request $request)
    {
        $request->validate([
            "id" => "required",
            "name" => "required",
            "email" => "required|email",
        ]);

        $user = User::where('id', $request->id)->where('is_admin', 1)->first();

        if (!$user) {
            return response([
                "message" => "Administrator not found",
                "visibility" => false
            ], 404);
        }

        $email = User::where('email', $request->email)
            ->where('id', '<>', $request->id)
            ->first();

        if ($email) {
            return response([
                "message" => "Email address already used",
                "visibility" => false
            ], 200);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        
        if (!empty($request->password)) {            
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response([
            "message" => "Administrator modified",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/admins/activate/{id}",
     *     tags = {"ADMINISTRATION_ADMINISTRATEUR"},
     *     summary="Active ou désactive l'état d'un administrateur par son ID",
     *      @OA\Parameter(
     *          name = "id",
     *          required = true,
     *          in = "path",
     *          example = 18,
     *          @OA\Schema(type="integer")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function activate($id)
    {
        $user = User::where('id', $id)->where("is_admin", 1)->first();

        if (!$user) {
            return response([
                "message" => "Administrator not found",
                "visibility" => false
            ], 404);
        }

        if ($user->is_active == 1) {
            $user->is_active = 0;
            $user->save();
            return response([
                "message" => "Administrator disabled",
                "visibility" => true
            ], 200);
        } else {
            $user->is_active = 1;
            $user->save();
            return response([
                "message" => "Administrator activated",
                "visibility" => true
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/admin/admins/delete",
     *     tags={"ADMINISTRATION__ADMINISTRATEURS"},
     *     summary="Supprimer un administrateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "id",
     *                   type="integer",
     *                   example = 10
     *                  ),
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function delete(Request $request)
    {
        $request->validate([
            "id" => "required"
        ]);

        $user = User::where('id', $request->id)->first();

        if (!$user) {
            return response([
                "message" => "Administrator not found",
                "visibility" => false
            ], 200);
        }

        $user->delete();

        return response([
            "message" => "Administrator deleted",
            "visibility" => true
        ], 200);
    }
}
