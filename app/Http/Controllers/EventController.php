<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Models\event;
use App\Models\image_event;
use DateTime;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class EventController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/events/list",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Liste des évènements",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function index()
    {
        $events =  event::all();

        return NewsResource::collection($events);
    }

    /**
     * @OA\Get(
     *     path="/admin/events/limit/{id}",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Renvoie les événement suivant une limite",
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
        $events = event::orderBy('date_event', 'desc')->take($id)->get();

        return NewsResource::collection($events);
    }

    /**
     * @OA\Post(
     *     path="/admin/events/search",
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
        
        $events = event::whereDate('date_event', $request->date)->get();

        return NewsResource::collection($events);
    }

    /**
     * @OA\Post(
     *     path="/admin/events/store",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Ajouter un évènement",
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
     *                   example = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore id recusandae a cupiditate dolorum doloribus velit nisi nesciunt itaque impedit quo, reiciendis molestiae rerum nihil eligendi ab exercitationem consectetur fugit!"
     *                  ),
     *                @OA\Property(
     *                   property = "user_id",
     *                   type="integer",
     *                   example = 1
     *                  ),
     *                @OA\Property(
     *                   property = "date",
     *                   type="date",
     *                   example = "2022-02-07 10:30"
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
            "date" => 'required',
            'cover' => 'nullable|mimes:jpeg,png,jpg|max:5120',
            'images.*' => 'nullable|mimes:jpeg,png,jpg|max:5120'
        ]);

        $event = new event();

        $event->titre = $request->title;
        $event->descriptions = $request->descriptions;
        $event->user_id = $request->user_id;
        $event->date_event = $request->date;
        $event->save();

        $cover = $request->file('cover');

        if (!empty($cover)) {
            $completeFileName = $cover->getClientOriginalName();

            $completeFileName = $cover->getClientOriginalName();
            $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
            $extension = $cover->getClientOriginalExtension();
            $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.' . $extension;

            $img = Image::make($cover);



            $img->save(public_path('storage/events/' . $compPic), 40);

            $image_event = new image_event();

            $image_event->chemin = $compPic;
            $image_event->event_id = $event->id;
            $image_event->couvert = 1;
            $image_event->save();
        }

        $images = $request->file('images');

        if (!empty($images)) {

            for ($i = 0; $i < count($images); $i++) {
                $completeFileName = $images[$i]->getClientOriginalName();

                $completeFileName = $images[$i]->getClientOriginalName();
                $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
                $extension = $images[$i]->getClientOriginalExtension();

                $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.' . $extension;

                $img = Image::make($images[$i]);



                $img->save(public_path('storage/events/' . $compPic), 40);

                $image_event = new image_event();

                $image_event->chemin = $compPic;
                $image_event->event_id = $event->id;
                $image_event->save();
            }
        }

        return response([
            "message" => "Event added",
            'visibility' => true
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/events/show/{id}",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Renvoie les informations d'un évènement par son ID",
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
        $event = event::find($id);

        if (!$event) {
            return response([
                "message" => "Event not found",
                'visibility' => false
            ], 404);
        }

        return NewsResource::make($event);
    }

    /**
     * @OA\Get(
     *     path="/admin/events/activate/{id}",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Active ou désactive l'état d'un évènement par son ID",
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
        $event = event::where('id', $id)->first();

        if (!$event) {
            return response([
                "message" => "Event not found",
                "visibility" => false
            ], 404);
        }

        if ($event->is_active == 1) {

            $event->is_active = 0;
            $event->save();

            return response([
                "message" => "Event disabled",
                "visibility" => true
            ], 200);
        } else {

            $event->is_active = 1;
            $event->save();

            return response([
                "message" => "Event actived",
                "visibility" => true
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/admin/events/update",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Editer les informations d'un évènement",
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
     *                   property = "titre",
     *                   type="string",
     *                   example = "Evangelisation"
     *                  ),
     *                @OA\Property(
     *                   property = "descriptions",
     *                   type="string",
     *                   example = "Une description"
     *                  ),
     *                @OA\Property(
     *                   property = "date",
     *                   type="date",
     *                   example = "2022-02-07 10:30"
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
            'id' => 'required',
            'title' => 'required',
            'descriptions' => 'required|string',
        ]);

        $event = event::find($request->id);

        if (!$event) {
            return response([
                "message" => "Event not found",
                "visibility" => false
            ], 200);
        }

        $event->titre = $request->title;
        $event->descriptions = $request->descriptions;

        if (!empty($request->date)) {
            $event->date_event = $request->date;
        }

        $event->save();

        return response([
            "message" => "Event modified",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/events/delete/image",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
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

        $image = image_event::find($request->id);

        if (!$image) {
            return response([
                "message" => "Event not found",
                "visibility" => false
            ], 200);
        }

        unlink(public_path('storage/events/' . $image->chemin));

        $image->delete();

        return response([
            "message" => "Image deleted",
            "visibility" => true
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/admin/events/update/image",
     *    tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Moofier une photo liée à un évènement",
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
     *                   property = "event_id",
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
            'event_id' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg|max:5120'
        ]);

        $img = image_event::find($request->id);

        $images = image_event::where($request->event_id)->get();

        if (!$img) {
            return response([
                "message" => "Event not found",
                "visibility" => false
            ], 200);
        }

        $image = $request->file('image');

        $completeFileName = $image->getClientOriginalName();
        $fileNameOnly = pathinfo($completeFileName, PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();

        $compPic = str_replace(' ', '_', $fileNameOnly) . '_' . rand() . '_' . time() . '.' . $extension;

        $image = Image::make($image);

        $image->resize(500, 500);

        $image->save(public_path('storage/events/' . $compPic), 40);

        unlink(public_path('storage/events/' . $img->chemin));

        $img->chemin = $compPic;
        $img->event_id = $request->event_id;

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
     *     path="/admin/events/delete",
     *     tags={"ADMINISTRATION__EVENEMENTS"},
     *     summary="Supprimer un événement",
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

        $user = event::where('id', $request->id)->first();

        if (!$user) {
            return response([
                "message" => "Event not found",
                "visibility" => false
            ], 200);
        }

        $user->delete();

        return response([
            "message" => "Event deleted",
            "visibility" => true
        ], 200);
    }
}
