<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function a() {
        try {
            $i = rand(1, 20);
            
            switch ($i) {
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                    $c = 'Cupom 10%';
                    break;
                case 7:
                case 8:
                    $c = 'Cumpo de 70%';
                    break;
                case 9: 
                    $c = 'Cupom de 90%';
                    break;
                case 10:
                case 11:
                case 12:
                case 13:
                case 14:
                case 15:
                    $c = 'Cupom de 30%';
                    break;
                case 16:
                case 17:
                case 18:
                case 19:
                case 20:         
                    $c = 'Cumpo de 50%';
                    break;
            };

            return response()->json([
                'Você ganhou um: ' => $c,
                'o numero: ' => $i
            ], 200);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
