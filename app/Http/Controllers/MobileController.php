<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\SupplierModel;
use App\Models\VoucherGenModel;
use App\Models\CommodityModel;
use App\Mail\OTPMail;
use File;
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
    private $commodity_model;

    public function __construct(UserModel $user_model, 
                                SupplierModel $supplier_model, 
                                VoucherGenModel $vouchergen_model, 
                                CommodityModel $commodity_model
                                ){
        $this->user_model = $user_model;    
        $this->supplier_model = $supplier_model;    
        $this->vouchergen_model = $vouchergen_model;    
        $this->commodity_model = $commodity_model;    
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

            return json_encode(array(["Message"=>"true","OTP"=>$random_password,"EMAIL"=>$to_email]));
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
           // Compute the balance of voucher    
            $get_voucher_amount = $this->vouchergen_model->where('REFERENCE_NO','DA566AB36L58O7M')->first()->AMOUNT;
            $compute_commodities= $this->commodity_model->where('REFERENCE_NO','DA566AB36L58O7M')->sum('amount');
            $check_balance = $get_voucher_amount - $compute_commodities;        
            $get_info[0]['Available_Balance'] = $check_balance;        
            return json_encode(array(["Message"=>'true',"data"=>$get_info]));    
        }else{
            return json_encode(array(["Message"=>'false']));    
        }

    }



     //  SUBMIT FUNCTION OF Claim Voucer
     public function submit_voucher(){

        $reference_num = request('reference_num');        
        $images_count = request('images_count');        
        $commodities = json_encode(request('commodities'));
        $decode = json_decode($commodities,true);
                

        
        // commodities
        foreach($decode as $item){
            $commodity = json_decode($item)->commodity;                        
        }


        // upload image
        for($i = 0 ; $i < $images_count ; $i++){
            $image = request()->input('image'.$i);
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'proof'.$reference_num .'-'.$i. '.jpeg';
            File::put(storage_path(). '/uploads//' . $imageName, base64_decode($image));            
        }
        
        

        
                        
    }

    public function otp()
    {
        return view('otp');
    }


    public function resendOTP(){

        $email = request('email');
        $random_password = mt_rand(1000,9999);
        
        Mail::send('otp', ["otp_code"=>$random_password], function ($message) use ($email,$random_password){
            $message->to($email)
                ->subject('VMP Mobile OTP')                                                     
                ->from("webdeveloper01000@gmail.com");                  
            });
        
        return json_encode(array(["Message"=>'true',"OTP"=>$random_password]));    
        
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
