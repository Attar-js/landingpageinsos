<?php



namespace App\Http\Controllers;



use Illuminate\Support\Facades\Auth;



class FaqController extends Controller

{

    /**

     * Menampilkan halaman FAQ dan tata cara

     */

    public function faqTataCara()

    {

        $user = Auth::user();



        return view('pages.faq-tata-cara', compact('user'));

    }

}


