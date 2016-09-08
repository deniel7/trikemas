<?php

namespace app\Providers;

use App\Item;
use Illuminate\Support\ServiceProvider;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        /* Item */
        Item::creating(function ($item) {
            $now = date('Y-m-d H:i:s');

            $item->model = strtoupper($item->model);
            $item->created_by = Auth::user()->id;
            $item->updated_by = Auth::user()->id;
            $item->created_at = $now;
            $item->updated_at = $now;
        });

        Item::updating(function ($item) {
            $now = date('Y-m-d H:i:s');

            $item->color = strtoupper($item->color);
            $item->model = strtoupper($item->model);
            $item->stok = $item->stok;
            $item->updated_by = Auth::user()->id;
            $item->updated_at = $now;
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }
}
