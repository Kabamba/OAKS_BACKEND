<?php

namespace App\Http\Controllers;

use App\Http\Resources\testimonialResource;
use App\Models\image_testimonial;
use App\Models\testimonial;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class TestimonialController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/testimonials/list",
     *    tags={"ADMINISTRATION__TEMOIGNAGES"},
     *     summary="Liste des témoignages",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $testimonials =  testimonial::all();

        return testimonialResource::collection($testimonials);
    }

    /**
     * @OA\Get(
     *     path="/admin/testimonials/limit/{id}",
     *    tags={"ADMINISTRATION__TEMOIGNAGES"},
     *     summary="Renvoie les témoignages suivant une limite",
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
        $testimonials = testimonial::orderBy('date_testi', 'desc')->take($id)->get();

        return testimonialResource::collection($testimonials);

    }

    /**
     * @OA\Post(
     *     path="/admin/testimonials/search",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Recherche un événement par sa date",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "date",
     *                   type="date",
     *                   example = "22/04/2022"
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
    public function searchDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);
        
        $testimonials = testimonial::whereDate('date_testi', $request->date)->get();

        return testimonialResource::collection($testimonials);
    }

    /**
     * @OA\Post(
     *     path="/admin/testimonials/store",
     *    tags={"ADMINISTRATION__TEMOIGNAGES"},
     *     summary="Ajouter un témoignage",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "title",
     *                   type="string",
     *                   example = "Evangelisation"
     *                  ),
     *                @OA\Property(
     *                   property = "descriptions",
     *                   type="string",
     *                   example = "Andy Kasanda"
     *                  ),
     *                 @OA\Property(
     *                   property = "witness_name",
     *                   type="string",
     *                   example = "Daniel Kiala"
     *                  ),
     *                @OA\Property(
     *                   property = "user_id",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
     *                   property = "date",
     *                   type="date",
     *                   example = "2022-04-05"
     *                  ),
     *                @OA\Property(
     *                   property = "cover",
     *                   type="file",
     *                  ),
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
            'title' => 'required',
            "descriptions" => 'required|string',
            'user_id' => 'required',
            'date' => 'required',
            'witness_name' => 'required',
            'cover' => 'required|mimes:jpeg,png,jpg|max:5120',
            'images.*' => 'nullable|mimes:jpeg,png,jpg|max:5120'
        ]);

        $testimonial = new testimonial();

        $testimonial->titre = $request->title;
        $testimonial->descriptions = $request->descriptions;
        $testimonial->witness_name = $request->witness_name;
        $testimonial->user_id = $request->user_id;
        $testimonial->date_testi = $request->date;
        $testimonial->save();

        $cover = $request->file('cover');

        if (!empty($cover)) {
            $completeFileName = $cover->getClientOriginalName();

            $completeFileName = $cover->getClientOriginalName();
            $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
            $extension = $cover->getClientOriginalExtension();
            $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time().'.'.$extension;

            $img = Image::make($cover);

            

            $img->save(public_path('storage/testimonials/' . $compPic), 40);

            $image_testimonial = new image_testimonial();

            $image_testimonial->chemin = $compPic;
            $image_testimonial->testimonial_id = $testimonial->id;
            $image_testimonial->covert = 1;
            $image_testimonial->save();
        }

        $images = $request->file('images');

        if (!empty($images)) {

            for ($i = 0; $i < count($images); $i++) {
                $completeFileName = $images[$i]->getClientOriginalName();

                $completeFileName = $images[$i]->getClientOriginalName();
                $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
                $extension = $images[$i]->getClientOriginalExtension();

                $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time().'.'.$extension;

                $img = Image::make($images[$i]);

                

                $img->save(public_path('storage/testimonials/' . $compPic), 40);

                $image_testimonial = new image_testimonial();

                $image_testimonial->chemin = $compPic;
                $image_testimonial->testimonial_id = $testimonial->id;
                $image_testimonial->save();
            }
        }

        return response([
            "message" => "Testimonial added",
            'visibility' => true
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/testimonials/show/{id}",
     *    tags={"ADMINISTRATION__TEMOIGNAGES"},
     *     summary="Renvoie les informations d'un témoignage par son ID",
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
        $testimonial = testimonial::find($id);

        if (!$testimonial) {
            return response([
                "message" => "Testimonial not found",
                'visibility' => false
            ], 404);
        }

        return testimonialResource::make($testimonial);
    }

    /**
     * @OA\Get(
     *     path="/admin/testimonials/activate/{id}",
     *    tags={"ADMINISTRATION__TEMOIGNAGES"},
     *     summary="Active ou désactive l'état d'un témoignage par son ID",
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
        $testimonial = testimonial::where('id', $id)->first();

        if (!$testimonial) {
            return response([
                "message" => "Testimonial not found",
                "visibility" => false
            ], 404);
        }

        if ($testimonial->is_active == 1) {

            $testimonial->is_active = 0;
            $testimonial->save();

            return response([
                "message" => "Testimonial disabled",
                "visibility" => true
            ], 200);
        } else {

            $testimonial->is_active = 1;
            $testimonial->save();

            return response([
                "message" => "Testimonial actived",
                "visibility" => true
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/admin/testimonials/update",
     *    tags={"ADMINISTRATION__TEMOIGNAGES"},
     *     summary="Editer les informations d'un témoignage",
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
     *                   property = "title",
     *                   type="string",
     *                   example = "Evangelisation"
     *                  ),
     *                @OA\Property(
     *                   property = "descriptions",
     *                   type="string",
     *                   example = "Une description"
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
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'title' => 'required',
            'descriptions' => 'required|string',
        ]);

        $testimonial = testimonial::find($request->id);

        if (!$testimonial) {
            return response([
                "message" => "Testimonial not found",
                "visibility" => false
            ], 200);
        }

        $testimonial->titre = $request->title;
        $testimonial->descriptions = $request->descriptions;
        $testimonial->save();

        return response([
            "message" => "Testimonial modified",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/testimonials/delete/image",
     *    tags={"ADMINISTRATION__TEMOIGNAGES"},
     *     summary="Supprimer une photo liée à un témoignage",
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

        $image = image_testimonial::find($request->id);

        if (!$image) {
            return response([
                "message" => "Testimonial not found",
                "visibility" => false
            ], 200);
        }

        unlink(public_path('storage/testimonials/' . $image->chemin));

        $image->delete();

        return response([
            "message" => "Image deleted",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/testimonials/update/image",
     *    tags={"ADMINISTRATION__TEMOIGNAGES"},
     *     summary="Moofier une photo liée à un témoignage",
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
     *                   property = "testimonial_id",
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
            'testimonial_id' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg|max:5120'
        ]);

        $img = image_testimonial::find($request->id);

        $images = image_testimonial::all();

        if (!$img) {
            return response([
                "message" => "Testimonial not found",
                "visibility" => false
            ], 200);
        }

        $image = $request->file('image');

        $completeFileName = $image->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();

        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time().'.'.$extension;

        $image = Image::make($image);

        $image->resize(500, 500);

        $image->save(public_path('storage/testimonials/' . $compPic), 40);

        unlink(public_path('storage/testimonials/' . $img->chemin));

        $img->chemin = $compPic;
        $img->testimonial_id = $request->testimonial_id;

        if (count($images) <= 1) {
            $img->couvert = $request->cover;
        }

        $img->save();

        return response([
            "message" => "Image modified",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/testimonial/delete",
     *     tags={"ADMINISTRATION__TEMOIGNAGES"},
     *     summary="Supprimer un témoignage",
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

        $testimonial = testimonial::where('id', $request->id)->first();

        if (!$testimonial) {
            return response([
                "message" => "Testimonial not found",
                "visibility" => false
            ], 200);
        }

        $testimonial->delete();

        return response([
            "message" => "Testimonial deleted",
            "visibility" => true
        ], 200);
    }
}
