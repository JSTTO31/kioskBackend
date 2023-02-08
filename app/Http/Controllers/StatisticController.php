<?php

namespace App\Http\Controllers;

use App\Repositories\StatisticRepository;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    private StatisticRepository $statisticRepository;

    public function __construct(){
        $this->statisticRepository = new StatisticRepository();
    }

    public function showTotalSales(Request $request){
        return $this->statisticRepository->totalSales();
    }

    public function showNumberOfOrders(Request $request){
        return $this->statisticRepository->numberOfOrders();
    }

    public function showProductTotal(){
        return $this->statisticRepository->numberOfProducts();
    }

    public function showProfit(){
        return $this->statisticRepository->profit();
    }
}
