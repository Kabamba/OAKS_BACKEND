<?php

namespace App\Http\Controllers;

use App\Models\galerie;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class GalerieController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/galeries/list",
     *     tags={"ADMINISTRATION__GALERIE"},
     *     summary="Liste des images de la galérie",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $galeries =  galerie::all();

        return $galeries;
    }

    /**
     * @OA\Post(
     *     path="/admin/galeries/store",
     *     tags={"ADMINISTRATION__GALERIE"},
     *     summary="Ajouter des images à la galérie",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "images[]",
     *                   type="array",
     *                     @OA\Items(
     *                          type = "string",
     *                          format = "binary"
     *                      )
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
            'images.*' => 'required|mimes:jpeg,png,jpg,webp|max:5120'
        ]);

        $images = $request->file('images');

        if (count($images) > 5) {
            return response([
                "message" => "Impossible to upload more than five images",
                "visibility" => false
            ], 200);
        }

        if (!empty($images)) {

            for ($i = 0; $i < count($images); $i++) {
                $completeFileName = $images[$i]->getClientOriginalName();

                $completeFileName = $images[$i]->getClientOriginalName();
                $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
                $extension = $images[$i]->getClientOriginalExtension();

                $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time().'.'.$extension;

                $img = Image::make($images[$i]);

                

                $img->save(public_path('storage/galerie/' . $compPic), 40);

                $galerie = new galerie();

                $galerie->chemin = $compPic;
                $galerie->save();
            }
        }

        return response([
            "message" => "Images saved",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/galeries/update/image",
     *    tags={"ADMINISTRATION__GALERIE"},
     *     summary="Moofier une image dans la galérie",
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
     *                 @OA\Property(
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
    public function update_img(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg|max:5120'
        ]);

        $img = galerie::find($request->id);

        if (!$img) {
            return response([
                "message" => "Image not found",
                "visibility" => false
            ], 200);
        }

        $image = $request->file('image');

        $completeFileName = $image->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time().'.'.$extension;

        $image = Image::make($image);

    
        $image->save(public_path('storage/galerie/' . $compPic), 40);

        unlink(public_path('storage/galerie/' . $img->chemin));

        $img->chemin = $compPic;
        $img->save();

        return response([
            "message" => "Image modified",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/galeries/delete/image",
     *    tags={"ADMINISTRATION__GALERIE"},
     *     summary="Supprimer une photo liée à un évènement",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "id",
     *                   type="integer",
     *                   example = 1
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
    public function delete_img(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $image = galerie::find($request->id);

        if (!$image) {
            return response([
                "message" => "Image not found",
                "visibility" => false
            ], 200);
        }

        unlink(public_path('storage/galerie/' . $image->chemin));

        $image->delete();

        return response([
            "message" => "Image deleted",
            "visibility" => true
        ], 200);
    }
}
