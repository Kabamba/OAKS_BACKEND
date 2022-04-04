<?php

namespace App\Http\Controllers;

use App\Jobs\NewsletterJob;
use App\Models\subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/subscribers/list",
     *    tags={"ADMINISTRATION__ABONNES"},
     *     summary="Liste des abonnés",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        return subscriber::all();
    }

    /**
     * @OA\Post(
     *     path="/admin/subscribers/store",
     *    tags={"ADMINISTRATION__ABONNES"},
     *     summary="Ajouter un abonné",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "email",
     *                   type="string",
     *                   example = "enockmulamba1802@gmail.com"
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
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $subscriber = new subscriber();

        $subscriber->email = $request->email;
        $subscriber->save();

        return response([
            "message" => "Subscription",
            "visibility" => false
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/subscribers/send",
     *    tags={"ADMINISTRATION__ABONNES"},
     *     summary="Liste des abonnés",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function send()
    {
        NewsletterJob::dispatch();
    }
}
