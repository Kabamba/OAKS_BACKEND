<?php

namespace App\Http\Controllers;

use App\Http\Resources\MinistryResource;
use App\Models\ministry;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class MinistryController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/ministries/list",
     *    tags={"ADMINISTRATION__MINISTERE"},
     *     summary="Liste des ministère",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $ministries = ministry::all();

        return MinistryResource::collection($ministries);
    }

    /**
     * @OA\Get(
     *     path="/admin/ministries/limit/{id}",
     *    tags={"ADMINISTRATION__MINISTERE"},
     *     summary="Renvoie les minitéres suivant une limite",
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
    public function limit($id)
    {
        $ministries = ministry::orderBy('created_at', 'desc')->take($id)->get();

        return MinistryResource::collection($ministries);
    }

    /**
     * @OA\Post(
     *     path="/admin/ministries/store",
     *    tags={"ADMINISTRATION__MINISTERE"},
     *     summary="Ajouter un ministère",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "ministry_name",
     *                   type="string",
     *                   example = "Evangelisation"
     *                  ),
     *                @OA\Property(
     *                   property = "leader_name",
     *                   type="string",
     *                   example = "Andy Kasanda"
     *                  ),
     *                @OA\Property(
     *                   property = "descriptions",
     *                   type="string",
     *                   example = "Un ministére puissant dans l'église où plusieurs âmes sont gagnées pour le christ"
     *                  ),
     *                @OA\Property(
     *                   property = "image",
     *                   type="file",
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
    public function store(Request $request)
    {
        $request->validate([
            'ministry_name' => 'required',
            "leader_name" => 'required|string',
            'descriptions' => 'required|string',
            'image' => 'required|mimes:jpeg,png,jpg|max:5120'
        ]);

        $ministry = new ministry();

        $image = $request->file('image');

        if (!empty($image)) {

            $completeFileName = $image->getClientOriginalName();

            $completeFileName = $image->getClientOriginalName();
            $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();

            $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.' . $extension;

            $img = Image::make($image);

            

            $img->save(public_path('storage/ministries/' . $compPic), 40);

            $ministry->image = $compPic;
        }

        $ministry->libelle = $request->ministry_name;
        $ministry->leader_name = $request->leader_name;
        $ministry->descriptions = $request->descriptions;
        $ministry->save();

        return response([
            "message" => "Ministry added",
            "visibility" => false
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/ministries/show/{id}",
     *    tags={"ADMINISTRATION__MINISTERE"},
     *     summary="Renvoie les informations d'un ministère par son ID",
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
        $ministry = ministry::find($id);

        if (!$ministry) {
            return response([
                "message" => "Ministry not found",
                "visibility" => false
            ], 404);
        }

        return MinistryResource::make($ministry);
    }

    /**
     * @OA\Post(
     *     path="/admin/ministries/update",
     *    tags={"ADMINISTRATION__MINISTERE"},
     *     summary="Editer les informations d'un ministère",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "id",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
     *                   property = "libelle",
     *                   type="string",
     *                   example = "Evangelisation"
     *                  ),
     *                @OA\Property(
     *                   property = "leader_name",
     *                   type="string",
     *                   example = "Andy Kasanda"
     *                  ),
     *                @OA\Property(
     *                   property = "descriptions",
     *                   type="string",
     *                   example = "Un ministére puissant dans l'église où plusieurs âmes sont gagnées pour le christ"
     *                  ),
     *                @OA\Property(
     *                   property = "image",
     *                   type="file",
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
    public function update(Request $request)
    {
        $request->validate([
            'ministry_name' => 'required',
            "leader_name" => 'required|string',
            'descriptions' => 'required|string',
            'image' => 'nullable|mimes:jpeg,png,jpg|max:5120'
        ]);

        $ministry = ministry::where('id', $request->id)->first();

        if (!$ministry) {
            return response([
                "message" => "Ministry not found",
                "visibility" => false
            ], 200);
        }


        $image = $request->file('image');

        if (!empty($image)) {

            $completeFileName = $image->getClientOriginalName();

            $completeFileName = $image->getClientOriginalName();
            $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.' . $extension;

            $img = Image::make($image);

            

            $img->save(public_path('storage/ministries/' . $compPic), 40);

            $ministry->image = $compPic;
        }

        $ministry->libelle = $request->ministry_name;
        $ministry->leader_name = $request->leader_name;
        $ministry->descriptions = $request->descriptions;
        $ministry->save();

        return response([
            "message" => "Ministry modified",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/ministries/activate/{id}",
     *    tags={"ADMINISTRATION__MINISTERE"},
     *     summary="Active ou désactive l'état d'un ministère par son ID",
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
        $ministry = ministry::where('id', $id)->first();

        if (!$ministry) {
            return response([
                "message" => "Ministry not found",
                "visibility" => false
            ], 404);
        }

        if ($ministry->is_active == 1) {

            $ministry->is_active = 0;
            $ministry->save();

            return response([
                "message" => "Ministry disabled",
                "visibility" => true
            ], 200);
        } else {

            $ministry->is_active = 1;
            $ministry->save();

            return response([
                "message" => "Ministry actived",
                "visibility" => true
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/admin/ministries/delete",
     *     tags={"ADMINISTRATION__MINISTERE"},
     *     summary="Supprimer un ministere",
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

        $user = ministry::where('id', $request->id)->first();

        if (!$user) {
            return response([
                "message" => "Ministry not found",
                "visibility" => false
            ], 200);
        }

        $user->delete();

        return response([
            "message" => "Ministry deleted",
            "visibility" => true
        ], 200);
    }
}
