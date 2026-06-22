<?php

use App\Http\Controllers\FactoryController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ShowroomController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\UpgradeController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    Route::get('/game', GameController::class)->name('game');

    Route::post('/game/upgrade/mine-speed/{resourceNode}', [UpgradeController::class, 'mineSpeed'])->name('upgrade.mine-speed');
    Route::post('/game/upgrade/mine-storage/{resourceNode}', [UpgradeController::class, 'mineStorage'])->name('upgrade.mine-storage');
    Route::post('/game/upgrade/factory-speed/{factoryLine}', [UpgradeController::class, 'factorySpeed'])->name('upgrade.factory-speed');
    Route::post('/game/upgrade/transport-capacity/{transportVehicle}', [UpgradeController::class, 'transportCapacity'])->name('upgrade.transport-capacity');
    Route::post('/game/upgrade/transport-speed/{transportVehicle}', [UpgradeController::class, 'transportSpeed'])->name('upgrade.transport-speed');
    Route::post('/game/upgrade/showroom-staff/{showroom}', [UpgradeController::class, 'showroomStaff'])->name('upgrade.showroom-staff');
    Route::post('/game/upgrade/showroom-marketing/{showroom}', [UpgradeController::class, 'showroomMarketing'])->name('upgrade.showroom-marketing');

    Route::post('/game/factory/{factoryLine}/queue', [FactoryController::class, 'queue'])->name('factory.queue');
    Route::post('/game/factory/{factoryLine}/quality', [FactoryController::class, 'toggleQuality'])->name('factory.toggle-quality');

    Route::post('/game/transport/{transportVehicle}/dispatch', [TransportController::class, 'dispatch'])->name('transport.dispatch');

    Route::post('/game/showroom/inventory/{showroomInventory}/price', [ShowroomController::class, 'setPrice'])->name('showroom.set-price');
    Route::post('/game/showroom/inventory/{showroomInventory}/reset-price', [ShowroomController::class, 'resetPrice'])->name('showroom.reset-price');
});

require __DIR__.'/settings.php';
