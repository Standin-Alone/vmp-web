<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\SupplierModel;
use App\Models\VoucherGenModel;
use App\Mail\OTPMail;
use Mail;
class MobileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $user_model;
    private $vouchergen_model;

    public function __construct(UserModel $user_model, SupplierModel $supplier_model, VoucherGenModel $vouchergen_model){
        $this->user_model = $user_model;    
        $this->supplier_model = $supplier_model;    
        $this->vouchergen_model = $vouchergen_model;    
    }

    public function index()
    {
        //

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return json_encode(array('message'=>'sample'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function sign_in(Request $request)
    {
        //

        $username = request('username');
        $password = request('password');

        $authenticate = $this->user_model->where('username',$username)->where('password',md5($password))->get();
        $random_password = mt_rand(1000,9999);

                 
        $to_email = "";
        if(!$authenticate->isEmpty()){

            foreach ($authenticate as $authenticate) {                 
                 $to_email=$this->supplier_model->where('SUPPLIER_COMPANY_NAME',$authenticate->company_name)->first()->COMPANY_EMAIL;
            }                
            
            Mail::send('otp', ["otp_code"=>$random_password], function ($message) use ($to_email,$random_password){
                $message->to($to_email)
                    ->subject('VMP Mobile OTP')                                                     
                    ->from("webdeveloper01000@gmail.com");
                  
              });

            return json_encode(array(["Message"=>"true","OTP"=>$random_password]));
        }else{
            return json_encode(array(["Message"=>"false"]));
        }

    }


    public function get_voucher_info(Request $request)
    {
        //

        $reference_num = request('reference_num');
        
        $get_info = $this->vouchergen_model->where('REFERENCE_NO',$reference_num)->get();
        
       if(!$get_info->isEmpty()){
            return json_encode(array(["Message"=>'true',"data"=>$get_info]));    
        }else{
            return json_encode(array(["Message"=>'false']));    
        }

    }


    public function otp()
    {
        return view('otp');
    }



    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
