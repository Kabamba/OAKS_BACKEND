<?php

namespace App\Http\Controllers;

use App\Http\Resources\SermonResource;
use App\Models\sermon;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class SermonController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/sermons/list",
     *    tags={"ADMINISTRATION__SERMONS"},
     *     summary="Liste des prédications",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $sermons = sermon::all();

        return SermonResource::collection($sermons);
    }

    /**
     * @OA\Get(
     *     path="/admin/sermons/limit/{id}",
     *    tags={"ADMINISTRATION__SERMONS"},
     *     summary="Renvoie les prédications suivant une limite",
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
        $sermons = sermon::orderBy('date_sermon', 'desc')->take($id)->get();

        return SermonResource::collection($sermons);

    }

    /**
     * @OA\Post(
     *     path="/admin/sermons/search/preacher",
     *    tags={"ADMINISTRATION__SERMONS"},
     *     summary="Recherche les prédications par le nom du prédicateur",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "preacher_name",
     *                   type="string",
     *                   example = "Dr. Athom's MBUMA"
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
    public function search_preacher(Request $request)
    {
        $request->validate([
            'preacher_name' => 'required|string',
        ]);

        $sermons = sermon::where('preacher_name', $request->preacher_name)->get();

        return SermonResource::collection($sermons);

    }

    /**
     * @OA\Post(
     *     path="/admin/sermons/search",
     *    tags={"ADMINISTRATION__SERMONS"},
     *     summary="Recherche une prédication par sa date",
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
        
        $sermons = sermon::whereDate('date_sermon', $request->date)->get();

        return SermonResource::collection($sermons);
    }

    /**
     * @OA\Get(
     *     path="/admin/sermons/preachers",
     *    tags={"ADMINISTRATION__SERMONS"},
     *     summary="Liste des prédications",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function preachers()
    {
        $preachers = sermon::select('preacher_name')->groupBy('preacher_name')->get()->toArray();

        return $preachers;
    }
    

    /**
     * @OA\Post(
     *     path="/admin/sermons/store",
     *    tags={"ADMINISTRATION__SERMONS"},
     *     summary="Ajouter une prédication",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *                @OA\Property(
     *                   property = "user_id",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
     *                   property = "title",
     *                   type="string",
     *                   example = "Spirituel"
     *                  ),
     *                @OA\Property(
     *                   property = "descriptions",
     *                   type="string",
     *                   example = "Une prédication qui nous parle des hommes de trois type d'homme qui exsitent : l'homme animal,l'homme charnel,l'homme spirituel"
     *                  ),
     *                @OA\Property(
     *                   property = "preacher_name",
     *                   type="string",
     *                   example = "Dr. Athom's MBUMA"
     *                  ),
     *                @OA\Property(
     *                   property = "url",
     *                   type="string",
     *                   example = "https://www.youtube.com/watch?v=l6TRxCTiJv8"
     *                  ),
     *                @OA\Property(
     *                   property = "image",
     *                   type="file"
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
            'url' => 'required|url',
            'user_id' =>  'required',
            'image' => 'required|mimes:jpeg,png,jpg|max:5120',
            'preacher_name' => 'required|string',
        ]);

        $sermon = new sermon();

        $image = $request->file('image');

        if (!empty($image)) {

            $completeFileName = $image->getClientOriginalName();

            $completeFileName = $image->getClientOriginalName();
            $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time().'.'.$extension;

            $img = Image::make($image);

            

            $img->save(public_path('storage/sermons/' . $compPic), 40);

            $sermon->image = $compPic;
        }

        $sermon->titre = $request->title;
        $sermon->user_id = $request->user_id;
        $sermon->url = $request->url;
        $sermon->descriptions = $request->descriptions;
        $sermon->preacher_name = $request->preacher_name;
        $sermon->save();

        return response([
            "message" => "Sermon added",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/sermons/show/{id}",
     *    tags={"ADMINISTRATION__SERMONS"},
     *     summary="Renvoie les informations d'une prédication par son ID",
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
        $sermon = sermon::find($id);

        if (!$sermon) {
            return response([
                "message" => "Sermon not found",
                "visibility" => false
            ], 404);
        }

        return SermonResource::make($sermon);
    }

    /**
     * @OA\Post(
     *     path="/admin/sermons/update",
     *    tags={"ADMINISTRATION__SERMONS"},
     *     summary="Editer les informations d'une prédication",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *              @OA\Schema(
     *              @OA\Property(
     *                   property = "sermon_id",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *               @OA\Property(
     *                   property = "user_id",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
     *                   property = "title",
     *                   type="string",
     *                   example = "Spirituel"
     *                  ),
     *                @OA\Property(
     *                   property = "descriptions",
     *                   type="string",
     *                   example = "Une prédication qui nous parle des hommes de trois type d'homme qui exsitent : l'homme animal,l'homme charnel,l'homme spirituel"
     *                  ),
     *                @OA\Property(
     *                   property = "preacher_name",
     *                   type="string",
     *                   example = "Dr. Athom's MBUMA"
     *                  ),
     *                @OA\Property(
     *                   property = "url",
     *                   type="string",
     *                   example = "https://www.youtube.com/watch?v=l6TRxCTiJv8"
     *                  ),
     *                @OA\Property(
     *                   property = "image",
     *                   type="file"
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
            'title' => 'required',
            "descriptions" => 'required|string',
            'url' => 'required|url',
            'user_id' =>  'required',
            'sermon_id' => 'required',
            'preacher_name' => 'required|string',
        ]);


        $sermon = sermon::where('id', $request->sermon_id)->first();

        if (!$sermon) {
            return response([
                "message" => "Sermon not found",
                "visibility" => false
            ], 200);
        }


        $image = $request->file('image');

        if (!empty($image)) {

            $completeFileName = $image->getClientOriginalName();

            $completeFileName = $image->getClientOriginalName();
            $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time().'.'.$extension;

            $img = Image::make($image);

            

            $img->save(public_path('storage/sermons/' . $compPic), 40);

            $sermon->image = $compPic;
        }

        $sermon->titre = $request->title;
        $sermon->user_id = $request->user_id;
        $sermon->url = $request->url;
        $sermon->descriptions = $request->descriptions;
        $sermon->preacher_name = $request->preacher_name;
        $sermon->save();

        return response([
            "message" => "Sermon modified",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/sermons/activate/{id}",
     *    tags={"ADMINISTRATION__SERMONS"},
     *     summary="Active ou désactive l'état d'une prédication par son ID",
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
        $sermon = sermon::where('id', $id)->first();

        if (!$sermon) {
            return response([
                "message" => "Sermon not found",
                "visibility" => false
            ], 404);
        }

        if ($sermon->is_active == 1) {

            $sermon->is_active = 0;
            $sermon->save();

            return response([
                "message" => "Sermon disabled",
                "visibility" => true
            ], 200);
        } else {

            $sermon->is_active = 1;
            $sermon->save();

            return response([
                "message" => "Sermon actived",
                "visibility" => true
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/admin/sermons/delete",
     *     tags={"ADMINISTRATION__SERMONS"},
     *     summary="Supprimer un sermon",
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

        $user = sermon::where('id', $request->id)->first();

        if (!$user) {
            return response([
                "message" => "Sermon not found",
                "visibility" => false
            ], 200);
        }

        $user->delete();

        return response([
            "message" => "Sermon deleted",
            "visibility" => true
        ], 200);
    }
}
