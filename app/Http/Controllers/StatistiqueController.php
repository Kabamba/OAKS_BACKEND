<?php

namespace App\Http\Controllers;

use App\Models\event;
use App\Models\galerie;
use App\Models\ministry;
use App\Models\sermon;
use Illuminate\Http\Request;

class StatistiqueController extends Controller
{

    /**
     * @OA\Get(
     *     path="/admin/stats/event",
     *    tags={"ADMINISTRATION__STATISTIQUES"},
     *     summary="Total des évenements",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function totEvent()
    {
        $event = event::all()->count();

        return $event;
    }

    /**
     * @OA\Get(
     *     path="/admin/stats/galerie",
     *    tags={"ADMINISTRATION__STATISTIQUES"},
     *     summary="Total des évenements",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function totGalerie()
    {
        $galerie = galerie::all()->count();

        return $galerie;
    }

    /**
     * @OA\Get(
     *     path="/admin/stats/sermon",
     *    tags={"ADMINISTRATION__STATISTIQUES"},
     *     summary="Total des sermons",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function totSermon()
    {
        $sermon = sermon::all()->count();
        return $sermon;
    }
        
    /**
     * @OA\Get(
     *     path="/admin/stats/ministries",
     *    tags={"ADMINISTRATION__STATISTIQUES"},
     *     summary="Total des ministéres",
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     )
     * )
     */
    public function totMinistries()
    {
        $ministries = ministry::all()->count();
        return $ministries;
    }
}
