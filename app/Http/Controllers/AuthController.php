<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/login",
     *     tags = {"CLIENT & ADMINISTRATEUR_AUTH"},
     *     summary="Authentifie l'utilisateur avant de se connecter",
     *     description="",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
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
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email|max:255",
            "password" => "required"
        ]);

        $credentials = $request->only("email", "password");

        if (!Auth::attempt($credentials)) {
            return response([
                "message" => "Email or password invalid",
                "visibility" => false,
            ], 200);
        }

        /**
         * @var User $user
         */
        $user = Auth::user();
        $token = $user->createToken($user->name);

        return response([
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "is_admin" => $user->is_admin,
            "visibility" => true,
            "is_active" => $user->is_active,
            "created_at" => $user->created_at,
            "update_at" => $user->update_at,
            "token" => $token->accessToken,
            "token_expires_at" => $token->token->expires_at
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     tags = {"CLIENT & ADMINISTRATEUR_AUTH"},
     *     summary="Deconnecte l'utilisateur",
     *     description="",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "token",
     *                   type="string",
     *                   schema="Bearer",
     *                   example = "89er4186gjazuihhiZIOJreioiouEIOJIOZERF814879AE8FEA"
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
    public function logout()
    {
        /**
         * @var user $user
         */
        $user = Auth::user();
        $user->tokens->each(function ($token) {
            $token->delete();
        });
        $userToken = $user->token();
        $userToken->delete();
        return response([
            "message" => "Déconnexion effectuée"
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     tags = {"CLIENT & ADMINISTRATEUR_AUTH"},
     *     summary="Enregister un utisaterur",
     *     description="",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="multipart/form-data",
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
    public function register(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed",
        ]);

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_admin = 0;
        $user->password = Hash::make($request->password);
        $user->save();

        return response(["message" => "Utilisateur enregistré avec succes"], 200);
    }

    /**
     * @OA\Post(
     *     path="/forgot",
     *     tags = {"CLIENT & ADMINISTRATEUR_AUTH"},
     *     summary="Mot de passe oublié",
     *     description="",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "email",
     *                   type="string",
     *                   example = "bgi@gmail.com"
     *                  )
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function forgot(Request $request)
    {
        $request->validate([
            "email" => "required|email"
        ]);

        $email = $request->email;

        if (User::where("email", $email)->doesntExist()) {
            return response([
                "message" => "Adresse mail introuvable",
                "type" => "danger",
                "visibility" => true
            ], 200);
        }

        $token = Str::random(10);

        DB::table("password_resets")->insert([
            "email" => $email,
            "token" => $token,
            "created_at" => now()->addHours(1)
        ]);

        Mail::send("mail.password_reset_mail", ["token" => $token], function ($message) use ($email) {
            $message->to($email);
            $message->subject("Rénitialisation du mot de passe");
        });

        return response([
            "message" => "Un email vous a été envoyé",
            "type" => "success",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/reset",
     *     tags = {"CLIENT & ADMINISTRATEUR_AUTH"},
     *     summary="Renitialiser le mot de passe",
     *     description="",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "token",
     *                   type="string",
     *                   example = "ufghzruqgioqzruogrjcuuUIBYRCUR?61154600"
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
     *                  )
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function reset(Request $request)
    {
        $request->validate([
            "token" => "required|string",
            "password" => "required|confirmed"
        ]);

        $token = $request->token;
        $passwordRest = DB::table("password_resets")->where("token", $token)->first();

        if (!$passwordRest) {
            return response([
                "message" => "Token introuvable",
                "type" => "danger",
                "visibility" => true
            ], 200);
        }

        if ($passwordRest->created_at <= now()) {
            return response([
                "message" => "Token expiré",
                "type" => "danger",
                "visibility" => true
            ], 200);
        }

        $user = User::where("email", $passwordRest->email)->first();

        if (!$user) {
            return response([
                "message" => "Utilisateur inconnu",
                "type" => "danger",
                "visibility" => true
            ], 200);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table("password_resets")->where("token", $token)->delete();

        return response([
            "message" => "Mot de passe rénitialisé avec succés",
            "type" => "success",
            "visibility" => true
        ], 200);
    }
}
