<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\SupplierModel;
use App\Models\VoucherGenModel;
use App\Models\CommodityModel;
use App\Models\AttachmentModel;
use App\Mail\OTPMail;
use File;
use Mail;
use URL;
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
    private $attachment_model;

    public function __construct(UserModel $user_model, 
                                SupplierModel $supplier_model, 
                                VoucherGenModel $vouchergen_model, 
                                CommodityModel $commodity_model,
                                AttachmentModel $attachment_model
                                ){
        $this->user_model = $user_model;    
        $this->supplier_model = $supplier_model;    
        $this->vouchergen_model = $vouchergen_model;    
        $this->commodity_model = $commodity_model;    
        $this->attachment_model = $attachment_model;    
    }


 

    public function sign_in(Request $request)
    {
        //

        $username = request('username');
        $password = request('password');

        $authenticate = $this->user_model->where('username',$username)->where('password',md5($password))->get();
        
        // $distributor_id = $this->supplier_model->where('SUPPLIER_COMPANY_NAME',$authenticate->company_name)->first()->DISTRIBUTOR_ID;
                            
        $random_password = mt_rand(1000,9999);
        

                 
        $to_email = "";
        if(!$authenticate->isEmpty()){
            $supplier_id = '';
            foreach ($authenticate as $authenticate) {                 
                 $to_email=$this->supplier_model->where('SUPPLIER_COMPANY_NAME',$authenticate->company_name)->first()->COMPANY_EMAIL;
                 $supplier_id = $this->supplier_model->where('SUPPLIER_COMPANY_NAME',$authenticate->company_name)->first()->id;
            }                
            
            Mail::send('otp', ["otp_code"=>$random_password], function ($message) use ($to_email,$random_password){
                $message->to($to_email)
                    ->subject('VMP Mobile OTP')                                                     
                    ->from("webdeveloper01000@gmail.com");                  
              });

            return json_encode(array(["Message"=>"true","OTP"=>$random_password,"EMAIL"=>$to_email,"SUPPLIER_ID" => $supplier_id]));
        }else{
            return json_encode(array(["Message"=>"false"]));
        }

    }

    public function get_scanned_vouchers()
    {
        
         
        $supplier_id = request('supplier_id');  
        $get_scanned_vouchers = $this->vouchergen_model->where('SUPPLIER_CODE',$supplier_id)->Where('VOUCHER_STATUS','CLAIMED')->take(5)->get(['REFERENCE_NO','CLAIMED_DATE']);
        
// ->orWhere('VOUCHER_STATUS','NOT FULLY CLAIMED')

        return json_encode($get_scanned_vouchers);


    }

    public function get_voucher_info(Request $request)
    {
        //

        $reference_num = request('reference_num');        
        
        $get_info = $this->vouchergen_model->where('REFERENCE_NO',$reference_num)->get();
        
        
       if(!$get_info->isEmpty()){
           // Compute the balance of voucher    
            $get_voucher_amount = $this->vouchergen_model->where('REFERENCE_NO',$reference_num)->first()->AMOUNT;
            // $compute_commodities= $this->commodity_model->where('REFERENCE_NO','DA566AB36L58O7M')->sum('amount');
            $check_balance = $get_voucher_amount;        
            $get_info[0]['Available_Balance'] = $check_balance;        
            return json_encode(array(["Message"=>'true',"data"=>$get_info]));    
        }else{
            return json_encode(array(["Message"=>'false']));    
        }

    }



     //  SUBMIT FUNCTION OF Claim Voucer
     public function submit_voucher(){

        try{
       
                $reference_num = request('reference_num');        
                $supplier_id = request('supplier_id');        
                $images_count = request('images_count');        
                $commodities = json_encode(request('commodities'));
                $decode = json_decode($commodities,true);
                $rsbsa_ctrl_no = $this->vouchergen_model->where('REFERENCE_NO',$reference_num)->first()->RSBSA_CTRL_NO;
                
                $update_current_balance = new VoucherGenModel();
                $commodities_total_amount = 0;
                // store commodities
                foreach($decode as $item){
                    $decoded_item = json_decode($item);

                    $store_commodities = new CommodityModel();
                    

                    $commodity = $decoded_item->commodity;
                    $unit = $decoded_item->unit;                        
                    $quantity = $decoded_item->quantity;                        
                    $amount = $decoded_item->amount;                        
                    $total_amount = $decoded_item->total_amount;                        

                    $commodities_total_amount += $total_amount;
                    
                
                    $store_commodities->fill([
                        "commodity" =>  $commodity,
                        "quantity" => $quantity,
                        "unit" => $unit,                                            
                        "amount" => $total_amount,
                        "REFERENCE_NO" => $reference_num,
                        "RSBSA_CTRL_NO" => $rsbsa_ctrl_no,
                        "DISTRIBUTOR_ID" => $supplier_id,
                        "SUPPLIER_CODE" => $supplier_id,
                        "SUPPLIER_GROUP" => $supplier_id
                        
                    ])->save();

                                             
                }
                $compute_balance = 0;
                                                
                $current_balance =  $this->vouchergen_model->where('REFERENCE_NO',$reference_num)->first()->AMOUNT;
                $compute_balance = $current_balance - $commodities_total_amount  ;
                

                $update_current_balance->where('REFERENCE_NO',$reference_num)->update(
                    [
                        "AMOUNT" => $compute_balance
                    ]);
            


                $document_type_value = '';
                
                // upload and store 
                for($i = 0 ; $i < $images_count ; $i++){
                    $image = request()->input('image'.$i);
                    $document_type = request('document_type'.$i);
                    $image = str_replace('data:image/jpeg;base64,', '', $image);
                    $image = str_replace(' ', '+', $image);
                    $imageName = $reference_num .'_'.$i. '.jpeg';
                    
                    $store_attachments = new  AttachmentModel();

                    if($document_type == 3)
                        $document_type_value = 'Picture of other documents or attachments';
                    else if ($document_type == 2)
                        $document_type_value = 'Picture of ID Presented and Signature';
                    else if ($document_type == 1 )
                        $document_type_value = 'Picture of farmer holding interventions';


                     $store_attachments->fill([
                            "att_file" =>  $imageName,
                            "requirement" => $document_type,
                            "filetitle" => $document_type_value,                                            
                            "REFERENCE_NO" => $reference_num,
                            "RSBSA_CTRL_NO" => $rsbsa_ctrl_no,
                            "imglink" => URL::to('/').'//storage//'. '/uploads//'.$imageName,
                            "SUPPLIER_CODE" => $supplier_id,
                            "DISTRIBUTOR_ID" => $supplier_id
                    ])->save();

                    
                    File::put(storage_path(). '/uploads//' . $imageName, base64_decode($image));            
                }
                

                echo json_encode(array(["Message"=>'true']));
        

        }catch(\Exception $e){
            echo json_encode(array(["Message"=>$e->getMessage(),"StatusCode" => $e->getCode()]));
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



  
}
